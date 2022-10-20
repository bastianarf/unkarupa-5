<?php

/**
 * Church Membership Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Church_Membership extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Church Membership Form', 'wpforms-form-templates-pack' );
		$this->slug = 'church-membership';
		$this->data = array(
			'field_id' => 10,
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
					'label' => esc_html__( 'Age', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'     => '3',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
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
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				6 => array(
					'id'      => '6',
					'type'    => 'radio',
					'label'   => esc_html__( 'How long have you been attending our church?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Less than a year', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '1-2 years', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '2-3 years', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'More than 3 years', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7 => array(
					'id'      => '7',
					'type'    => 'radio',
					'label'   => esc_html__( 'Will you be leaving another church?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'No', 'wpforms-form-templates-pack' ),
						),
					),
				),
				8 => array(
					'id'    => '8',
					'type'  => 'text',
					'label' => esc_html__( 'If yes', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9 => array(
					'id'    => '9',
					'type'  => 'textarea',
					'label' => esc_html__( 'Why do you want to be a church member?', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Church_Membership;
