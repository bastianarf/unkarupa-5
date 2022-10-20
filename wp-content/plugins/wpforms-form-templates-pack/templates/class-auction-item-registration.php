<?php

/**
 * Auction Item Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Auction_Item_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Auction Item Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'auction-item-registration';
		$this->data = array(
			'field_id' => 10,
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
					'id'     => '3',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				4 => array(
					'id'     => '4',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				5 => array(
					'id'            => '5',
					'type'          => 'divider',
					'label'         => esc_html__( 'Auction Item Information', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'Item Name', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7 => array(
					'id'     => '7',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Estimated Value', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				8 => array(
					'id'    => '8',
					'type'  => 'file-upload',
					'label' => esc_html__( 'Image', 'wpforms-form-templates-pack' ),
				),
				9 => array(
					'id'    => '9',
					'type'  => 'textarea',
					'label' => esc_html__( 'Detailed Description', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Auction_Item_Registration;
