<?php

/**
 * Vendor Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Vendor_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Vendor Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'vendor-registration';
		$this->data = array(
			'field_id' => 10,
			'fields'   => array(
				1 => array(
					'id'    => '1',
					'type'  => 'text',
					'label' => esc_html__( 'Company\'s Legal Name', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2 => array(
					'id'      => '2',
					'type'    => 'select',
					'label'   => esc_html__( 'Business Type', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Manufacturer', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Distributor', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Service Provider', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				3 => array(
					'id'    => '3',
					'type'  => 'textarea',
					'label' => esc_html__( 'What kind of products/services does your company offer?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'url',
					'label' => esc_html__( 'Company Website', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'number',
					'label' => esc_html__( 'Company Founding Year', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'     => '6',
					'type'   => 'address',
					'label'  => esc_html__( 'Company Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				7 => array(
					'id'       => '7',
					'type'     => 'name',
					'label'    => esc_html__( 'Name of person representing the company', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				8 => array(
					'id'     => '8',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				9 => array(
					'id'       => '9',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Vendor_Registration;
