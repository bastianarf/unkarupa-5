<?php

/**
 * Gym Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Gym_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Gym Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'gym-registration';
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
					'id'            => '3',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Membership Start Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				4 => array(
					'id'      => '4',
					'type'    => 'radio',
					'label'   => esc_html__( 'Membership Duration', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( '1 month', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '3 month', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( '6 months', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '1 year', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( '2 year', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Lifetime', 'wpforms-form-templates-pack' ),
						),
					),
				),
				6 => array(
					'id'            => '6',
					'type'          => 'checkbox',
					'label'         => esc_html__( 'Pre-existing medical conditions', 'wpforms-form-templates-pack' ),
					'choices'       => array(
						1  => array(
							'label' => esc_html__( 'Diabetes', 'wpforms-form-templates-pack' ),
						),
						2  => array(
							'label' => esc_html__( 'Heart disease', 'wpforms-form-templates-pack' ),
						),
						3  => array(
							'label' => esc_html__( 'Chest pains', 'wpforms-form-templates-pack' ),
						),
						4  => array(
							'label' => esc_html__( 'Shortness of breath', 'wpforms-form-templates-pack' ),
						),
						12 => array(
							'label' => esc_html__( 'Broken bones', 'wpforms-form-templates-pack' ),
						),
						11 => array(
							'label' => esc_html__( 'Allergies', 'wpforms-form-templates-pack' ),
						),
						10 => array(
							'label' => esc_html__( 'Heart murmur', 'wpforms-form-templates-pack' ),
						),
						9  => array(
							'label' => esc_html__( 'Pneumonia', 'wpforms-form-templates-pack' ),
						),
						8  => array(
							'label' => esc_html__( 'Epilepsy', 'wpforms-form-templates-pack' ),
						),
						7  => array(
							'label' => esc_html__( 'Tachycardia', 'wpforms-form-templates-pack' ),
						),
						6  => array(
							'label' => esc_html__( 'Oedema', 'wpforms-form-templates-pack' ),
						),
						5  => array(
							'label' => esc_html__( 'Heart attack', 'wpforms-form-templates-pack' ),
						),
						17 => array(
							'label' => esc_html__( 'Recent surgery', 'wpforms-form-templates-pack' ),
						),
						16 => array(
							'label' => esc_html__( 'Palpitations', 'wpforms-form-templates-pack' ),
						),
						15 => array(
							'label' => esc_html__( 'High blood pressure', 'wpforms-form-templates-pack' ),
						),
						14 => array(
							'label' => esc_html__( 'Low blood pressure', 'wpforms-form-templates-pack' ),
						),
						13 => array(
							'label' => esc_html__( 'Asthma', 'wpforms-form-templates-pack' ),
						),
						18 => array(
							'label' => esc_html__( 'Seizures', 'wpforms-form-templates-pack' ),
						),
						19 => array(
							'label' => esc_html__( 'Fainting', 'wpforms-form-templates-pack' ),
						),
					),
					'input_columns' => '2',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'textarea',
					'label' => esc_html__( 'Additional Medical Notes', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Gym_Registration;
