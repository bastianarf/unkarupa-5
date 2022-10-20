<?php

/**
 * Church Donation form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Church_Donation extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Church Donation Form', 'wpforms-form-templates-pack' );
		$this->slug = 'church-donation';
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
				3 => array(
					'id'       => '3',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'payment-single',
					'label'    => esc_html__( 'Donation Amount', 'wpforms-form-templates-pack' ),
					'price'    => '0.00',
					'format'   => 'user',
					'required' => '1',
					'size'     => 'medium',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'text',
					'label' => esc_html__( 'This donation is in memory of', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8 => array(
					'id'      => '8',
					'type'    => 'radio',
					'label'   => esc_html__( 'Please direct my gift to one of the following areas', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Youth', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Family', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Seniors', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Ministry', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Where it is needed most', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5 => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'Additional Comments', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Church_Donation;
