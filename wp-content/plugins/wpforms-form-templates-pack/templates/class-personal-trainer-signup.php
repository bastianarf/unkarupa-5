<?php

/**
 * Personal Trainer Sign-Up Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Personal_Trainer_Signup extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Personal Trainer Sign-Up Form', 'wpforms-form-templates-pack' );
		$this->slug = 'personal-trainer-sign-up';
		$this->data = array(
			'field_id' => 12,
			'fields'   => array(
				11 => array(
					'id'       => '11',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'    => '2',
					'type'  => 'number',
					'label' => esc_html__( 'Age', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3  => array(
					'id'    => '3',
					'type'  => 'number',
					'label' => esc_html__( 'Weight (lbs)', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4  => array(
					'id'     => '4',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				5  => array(
					'id'       => '5',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				6  => array(
					'id'     => '6',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				7  => array(
					'id'      => '7',
					'type'    => 'radio',
					'label'   => esc_html__( 'Trainer Preference', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Male', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Female', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'No preference', 'wpforms-form-templates-pack' ),
						),
					),
				),
				8  => array(
					'id'      => '8',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Desired Days', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Monday', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Tuesday', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Wednesday', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Thursday', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Friday', 'wpforms-form-templates-pack' ),
						),
					),
				),
				9  => array(
					'id'    => '9',
					'type'  => 'text',
					'label' => esc_html__( 'Desired Times', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				10 => array(
					'id'      => '10',
					'type'    => 'radio',
					'label'   => esc_html__( 'Select a package', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Fitness Assessment', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'One session', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '4 weekly sessions', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( '12 weekly sessions', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Personal_Trainer_Signup;
