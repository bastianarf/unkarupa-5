<?php

/**
 * Sponsorship Request Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Sponsorship_Request extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Sponsorship Request Form', 'wpforms-form-templates-pack' );
		$this->slug = 'sponsorship-request';
		$this->data = array(
			'field_id' => 9,
			'fields'   => array(
				1 => array(
					'id'    => '1',
					'type'  => 'text',
					'label' => esc_html__( 'Company or Group Requesting Sponsorship', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'       => '3',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				4 => array(
					'id'     => '4',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				5 => array(
					'id'      => '5',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Preferred Contact Method', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'No preference', 'wpforms-form-templates-pack' ),
						),
					),
				),
				6 => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'Please describe the event or program that the sponsorship will support.', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7 => array(
					'id'      => '7',
					'type'    => 'radio',
					'label'   => esc_html__( 'Please indicate which sponsorship level you are seeking.', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Platinum', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Gold', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Silver', 'wpforms-form-templates-pack' ),
						),
					),
				),
				8 => array(
					'id'    => '8',
					'type'  => 'textarea',
					'label' => esc_html__( 'Additional Comments / Requests', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Sponsorship_Request;
