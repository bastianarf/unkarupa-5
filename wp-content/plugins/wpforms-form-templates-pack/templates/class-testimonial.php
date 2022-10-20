<?php

/**
 * Testimonial Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Testimonial extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Testimonial Form', 'wpforms-form-templates-pack' );
		$this->slug = 'testimonial';
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
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Company', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'text',
					'label' => esc_html__( 'Job Title', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5 => array(
					'id'      => '5',
					'type'    => 'select',
					'label'   => esc_html__( 'Which product/service do you use?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Product A', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Product B', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Service A', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Service B', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'Tell us what you think about the product/service.', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7 => array(
					'id'      => '7',
					'type'    => 'radio',
					'label'   => esc_html__( 'May we post your testimonial (or a portion of it) on our website?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'No', 'wpforms-form-templates-pack' ),
						),
					),
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

new WPForms_Template_Testimonial;
