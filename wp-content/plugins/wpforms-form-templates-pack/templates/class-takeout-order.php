<?php

/**
 * Takeout Order form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Takeout_Order extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Takeout Order Form', 'wpforms-form-templates-pack' );
		$this->slug = 'takeout-order';
		$this->data = array(
			'field_id' => '7',
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'select',
					'label'    => esc_html__( 'What type of pizza would you like?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Cheese', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Pepperoni (+ $0.50)', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Sausage (+ $0.50)', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'radio',
					'label'    => esc_html__( 'How many pizzas would you like to order?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( '1 - $10', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '2 - $20', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '3 - $30', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				3 => array(
					'id'       => '3',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				4 => array(
					'id'       => '4',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				5 => array(
					'id'       => '5',
					'type'     => 'phone',
					'label'    => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				6 => array(
					'id'     => '6',
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

new WPForms_Template_Takeout_Order;
