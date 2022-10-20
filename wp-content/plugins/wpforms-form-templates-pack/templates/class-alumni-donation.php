<?php

/**
 * Alumni Donation form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Alumni_Donation extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Alumni Donation Form', 'wpforms-form-templates-pack' );
		$this->slug = 'alumni-donation';
		$this->data = array(
			'field_id' => 6,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'number',
					'label' => esc_html__( 'Class Year', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'       => '3',
					'type'     => 'phone',
					'label'    => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				4 => array(
					'id'       => '4',
					'type'     => 'payment-single',
					'label'    => esc_html__( 'Donation Amount', 'wpforms-form-templates-pack' ),
					'format'   => 'user',
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

new WPForms_Template_Alumni_Donation;
