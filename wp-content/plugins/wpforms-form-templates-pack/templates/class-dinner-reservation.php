<?php

/**
 * Dinner Reservation Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Dinner_Reservation extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Dinner Reservation Form', 'wpforms-form-templates-pack' );
		$this->slug = 'dinner-reservation';
		$this->data = array(
			'field_id' => 12,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				9  => array(
					'id'       => '9',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				8  => array(
					'id'       => '8',
					'type'     => 'phone',
					'label'    => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				3  => array(
					'id'      => '3',
					'type'    => 'radio',
					'label'   => esc_html__( 'How would you prefer to get your reservation approval? *', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
						),
					),
				),
				2  => array(
					'id'      => '2',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Diet Restrictions', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Vegetarian', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Gluten-free', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Dairy-free', 'wpforms-form-templates-pack' ),
						),
					),
				),
				10 => array(
					'id'            => '10',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				11 => array(
					'id'      => '11',
					'type'    => 'select',
					'label'   => esc_html__( 'What time would you like to join us?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( '5:00pm', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '5:30pm', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '6:00pm', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( '6:30pm', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( '7:00pm', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( '7:30pm', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( '8:00pm', 'wpforms-form-templates-pack' ),
						),
						8 => array(
							'label' => esc_html__( '8:30pm', 'wpforms-form-templates-pack' ),
						),
						9 => array(
							'label' => esc_html__( '9:00pm', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				5  => array(
					'id'      => '5',
					'type'    => 'radio',
					'label'   => esc_html__( 'Where would you like to sit?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Inside', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Outside patio', 'wpforms-form-templates-pack' ),
						),
					),
				),
				6  => array(
					'id'      => '6',
					'type'    => 'radio',
					'label'   => esc_html__( 'Sitting preference', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Table', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Booth', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7  => array(
					'id'    => '7',
					'type'  => 'textarea',
					'label' => esc_html__( 'Additional requests', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Dinner_Reservation;
