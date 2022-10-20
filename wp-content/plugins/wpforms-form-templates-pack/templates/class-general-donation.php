<?php

/**
 * General Donation form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_General_Donation extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'General Donation Form', 'wpforms-form-templates-pack' );
		$this->slug = 'general-donation';
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
					'type'     => 'payment-single',
					'label'    => esc_html__( 'Donation Amount', 'wpforms-form-templates-pack' ),
					'price'    => '0.00',
					'format'   => 'user',
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
				6 => array(
					'id'         => '6',
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Other Options', 'wpforms-form-templates-pack' ),
					'choices'    => array(
						1 => array(
							'label' => esc_html__( 'I prefer to make my donation anonymously.', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Please sign me up to the newsletter.', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'I wish to volunteer to help with fundraising.', 'wpforms-form-templates-pack' ),
						),
					),
					'label_hide' => '1',
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

new WPForms_Template_General_Donation;
