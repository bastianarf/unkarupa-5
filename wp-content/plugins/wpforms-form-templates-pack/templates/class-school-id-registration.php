<?php

/**
 * School ID Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_School_ID_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'School ID Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'school-id-registration';
		$this->data = array(
			'field_id' => 7,
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
					'id'    => '2',
					'type'  => 'number',
					'label' => esc_html__( 'Expected Year of Graduation', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'     => '3',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				4 => array(
					'id'       => '4',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				5 => array(
					'id'     => '5',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'School ID', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_School_ID_Registration;
