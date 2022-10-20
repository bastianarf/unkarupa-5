<?php

/**
 * Conference Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Conference_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Conference Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'conference-registration';
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
					'id'       => '3',
					'type'     => 'radio',
					'label'    => esc_html__( 'Which access pass would you like to purchase?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Bronze - $199.95', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Silver - $299.95', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Gold - $399.95', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				4 => array(
					'id'       => '4',
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Which sessions do you plan on attending?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Session 1', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Session 2', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Session 3', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Session 4', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Session 5', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				5 => array(
					'id'       => '5',
					'type'     => 'radio',
					'label'    => esc_html__( 'Will you be staying overnight?', 'wpforms-form-templates-pack' ),
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
				6 => array(
					'id'         => '6',
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Email Newsletter', 'wpforms-form-templates-pack' ),
					'choices'    => array(
						1 => array(
							'label' => esc_html__( 'I would like to receive email updates regarding future conferences', 'wpforms-form-templates-pack' ),
						),
					),
					'label_hide' => '1',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'textarea',
					'label' => esc_html__( 'Comments or Questions', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Conference_Registration;
