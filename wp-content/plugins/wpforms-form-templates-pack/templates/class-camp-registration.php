<?php

/**
 * Camp Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Camp_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Camp Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'camp-registration';
		$this->data = array(
			'field_id' => 13,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Camper Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'       => '2',
					'type'     => 'select',
					'label'    => esc_html__( 'Camper Age Group', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( '6-8 years old', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '9-12 years old', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '13-15 years old', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
					'size'     => 'medium',
				),
				3  => array(
					'id'      => '3',
					'type'    => 'radio',
					'label'   => esc_html__( 'Desired Cabin', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Cabin 1', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Cabin 2', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Cabin 3', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Cabin 4', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Cabin 5', 'wpforms-form-templates-pack' ),
						),
					),
				),
				4  => array(
					'id'       => '4',
					'type'     => 'radio',
					'label'    => esc_html__( 'Does the camper have any known allergies?', 'wpforms-form-templates-pack' ),
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
				5  => array(
					'id'       => '5',
					'type'     => 'radio',
					'label'    => esc_html__( 'Has the camper been camping before?', 'wpforms-form-templates-pack' ),
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
				6  => array(
					'id'            => '6',
					'type'          => 'divider',
					'label'         => esc_html__( 'Emergency Contact Information', 'wpforms-form-templates-pack' ),
					'description'   => 'Please enter the name of the parent or guardian who should be contacted in the case of an emergency.',
					'label_disable' => '1',
				),
				7  => array(
					'id'       => '7',
					'type'     => 'radio',
					'label'    => esc_html__( 'Relationship', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Parent', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Sibling', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Relative', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Friend', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				9  => array(
					'id'       => '9',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				10 => array(
					'id'       => '10',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				11 => array(
					'id'     => '11',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				12 => array(
					'id'    => '12',
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

new WPForms_Template_Camp_Registration;
