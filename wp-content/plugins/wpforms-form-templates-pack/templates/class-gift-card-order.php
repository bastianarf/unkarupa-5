<?php

/**
 * Gift Card Order form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Gift_Card_Order extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Gift Card Order Form', 'wpforms-form-templates-pack' );
		$this->slug = 'gift-card-order';
		$this->data = array(
			'field_id' => '10',
			'fields'   => array(
				9 => array(
					'id'       => '9',
					'type'     => 'payment-single',
					'label'    => esc_html__( 'Gift Card Amount', 'wpforms-form-templates-pack' ),
					'format'   => 'user',
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'       => '3',
					'type'     => 'name',
					'label'    => esc_html__( 'Recipient\'s Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				4 => array(
					'id'       => '4',
					'type'     => 'address',
					'label'    => esc_html__( 'Shipping', 'wpforms-form-templates-pack' ),
					'scheme'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				5 => array(
					'id'       => '5',
					'type'     => 'name',
					'label'    => esc_html__( 'Purchaser\'s Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				6 => array(
					'id'       => '6',
					'type'     => 'address',
					'label'    => esc_html__( 'Billing Address', 'wpforms-form-templates-pack' ),
					'scheme'   => 'us',
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
					'id'       => '8',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Gift_Card_Order;
