<?php

/**
 * Race Registration form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Race_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Race Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'race-registration';
		$this->data = array(
			'field_id' => 9,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Which race will you participate in?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( '5k', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '10k', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Half Marathon', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Marathon', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				2 => array(
					'id'            => '2',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date of Birth', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'required'      => '1',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'dropdown',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				5 => array(
					'id'      => '5',
					'type'    => 'select',
					'label'   => esc_html__( 'What size T-shirt do you wear?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Small', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Medium', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Large', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Extra Large', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				4 => array(
					'id'       => '4',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				6 => array(
					'id'       => '6',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				7 => array(
					'id'     => '7',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				8 => array(
					'id'     => '8',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
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

new WPForms_Template_Race_Registration;
