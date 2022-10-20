<?php

/**
 * Webinar Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Webinar_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Webinar Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'webinar-registration';
		$this->data = array(
			'field_id' => 5,
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
					'id'      => '4',
					'type'    => 'radio',
					'label'   => esc_html__( 'How did you hear about this webinar?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Social media', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Word of mouth', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Advertisement', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Webinar_Registration;
