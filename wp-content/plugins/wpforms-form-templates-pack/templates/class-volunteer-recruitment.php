<?php

/**
 * Volunteer Recruitment Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Volunteer_Recruitment extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Volunteer Recruitment Form', 'wpforms-form-templates-pack' );
		$this->slug = 'volunteer-recruitment';
		$this->data = array(
			'field_id' => 11,
			'fields'   => array(
				1  => array(
					'id'      => '1',
					'type'    => 'radio',
					'label'   => esc_html__( 'Which programs are you interested in volunteering for?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Homelessness', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Child abuse and neglect', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Drug and alcohol addiction', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Medical research', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Health and safety', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				2  => array(
					'id'      => '2',
					'type'    => 'radio',
					'label'   => esc_html__( 'Which events are you interested in volunteering for?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Annual walk-a-thon', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Quarterly raffle', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Black-tie Gala', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				3  => array(
					'id'      => '3',
					'type'    => 'radio',
					'label'   => esc_html__( 'How many hours per week would you be able to dedicate?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( '3 hour or less', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '3 to 5 hours', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '5 to 10 hours', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( '10 to 20 hours', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( '20 or more hours', 'wpforms-form-templates-pack' ),
						),
					),
				),
				4  => array(
					'id'      => '4',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'What days of the week are you available?', 'wpforms-form-templates-pack' ),
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
						6 => array(
							'label' => esc_html__( 'Saturday', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Sunday', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5  => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'Please list any relevant experiences you have that you feel would benefit any of the above programs or events.', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6  => array(
					'id'      => '6',
					'type'    => 'radio',
					'label'   => esc_html__( 'How did you find out about our organization?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Direct mail', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Advertisement', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Online Search', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Friend / Family', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Business Colleague ', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7  => array(
					'id'       => '7',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				8  => array(
					'id'     => '8',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				9  => array(
					'id'       => '9',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				10 => array(
					'id'       => '10',
					'type'     => 'phone',
					'label'    => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format'   => 'us',
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

new WPForms_Template_Volunteer_Recruitment;
