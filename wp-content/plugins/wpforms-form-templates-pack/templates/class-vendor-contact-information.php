<?php

/**
 * Vendor Contact Information Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Vendor_Contact_Information extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Vendor Contact Information Form', 'wpforms-form-templates-pack' );
		$this->slug = 'vendor-contact-information';
		$this->data = array(
			'field_id' => 8,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'text',
					'label'    => esc_html__( 'Vendor Name', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'    => '2',
					'type'  => 'url',
					'label' => esc_html__( 'Website / URL', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'       => '3',
					'type'     => 'name',
					'label'    => esc_html__( 'Contact Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				4 => array(
					'id'     => '4',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				5 => array(
					'id'       => '5',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				7 => array(
					'id'     => '7',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'Comments', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Vendor_Contact_Information;
