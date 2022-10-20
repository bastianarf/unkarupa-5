<?php

/**
 * Avon Order form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Avon_Order extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Avon Order Form', 'wpforms-form-templates-pack' );
		$this->slug = 'avon-order';
		$this->data = array(
			'field_id' => 11,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'text',
					'label'    => esc_html__( 'Catalogue Name', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'    => '2',
					'type'  => 'number',
					'label' => esc_html__( 'Page Number', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3  => array(
					'id'       => '3',
					'type'     => 'number',
					'label'    => esc_html__( 'Quantity', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				4  => array(
					'id'    => '4',
					'type'  => 'text',
					'label' => esc_html__( 'Product #', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5  => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'Product Description', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6  => array(
					'id'     => '6',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Cost', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				7  => array(
					'id'       => '7',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				8  => array(
					'id'       => '8',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				9  => array(
					'id'     => '9',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				10 => array(
					'id'     => '10',
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

new WPForms_Template_Avon_Order;
