<?php

/**
 * Rent Receipt Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Rent_Receipt extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Rent Receipt Form', 'wpforms-form-templates-pack' );
		$this->slug = 'rent-receipt';
		$this->data = array(
			'field_id' => 9,
			'fields'   => array(
				1 => array(
					'id'            => '1',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date / Time', 'wpforms-form-templates-pack' ),
					'format'        => 'date-time',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'name',
					'label'    => esc_html__( 'Tenant Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				8 => array(
					'id'       => '8',
					'type'     => 'email',
					'label'    => esc_html__( 'Tenant Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'     => '3',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Amount Paid', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				4 => array(
					'id'     => '4',
					'type'   => 'address',
					'label'  => esc_html__( 'Property Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				5 => array(
					'id'      => '5',
					'type'    => 'radio',
					'label'   => esc_html__( 'Payment Type', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Cash', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Check', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Money order', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7 => array(
					'id'       => '7',
					'type'     => 'name',
					'label'    => esc_html__( 'Payment Received By', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
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

new WPForms_Template_Rent_Receipt;
