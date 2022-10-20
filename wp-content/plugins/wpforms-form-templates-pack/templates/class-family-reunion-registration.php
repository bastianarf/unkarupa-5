<?php

/**
 * Family Reunion Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Family_Reunion_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Family Reunion Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'family-reunion-registration';
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
					'id'       => '2',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'     => '3',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				4 => array(
					'id'      => '4',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'What activities do you want to play?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Football', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Baseball', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Kickball', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Volleyball', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5 => array(
					'id'      => '5',
					'type'    => 'radio',
					'label'   => esc_html__( 'What will you be bringing?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Snacks', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Lunch', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Drinks', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Dessert', 'wpforms-form-templates-pack' ),
						),
					),
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

new WPForms_Template_Family_Reunion_Registration;
