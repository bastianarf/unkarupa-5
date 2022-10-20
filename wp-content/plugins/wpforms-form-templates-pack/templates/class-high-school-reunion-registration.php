<?php

/**
 * High School Reunion Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_High_School_Reunion_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'High School Reunion Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'high-school-reunion-registration';
		$this->data = array(
			'field_id' => 9,
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
					'id'     => '2',
					'type'   => 'name',
					'label'  => esc_html__( 'Spouse or Guest Name', 'wpforms-form-templates-pack' ),
					'format' => 'first-last',
					'size'   => 'medium',
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
				6 => array(
					'id'      => '6',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Which events will you be attending?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Friday night cookout', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Saturday breakfast', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Saturday lunch', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Saturday formal dinner', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7 => array(
					'id'       => '7',
					'type'     => 'radio',
					'label'    => esc_html__( 'Would you like to pre-order a reunion DVD ($19.95)?', 'wpforms-form-templates-pack' ),
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
				8 => array(
					'id'    => '8',
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

new WPForms_Template_High_School_Reunion_Registration;
