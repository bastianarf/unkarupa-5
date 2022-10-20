<?php

/**
 * Tshirt Order form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Tshirt_Order extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'T-Shirt Order Form', 'wpforms-form-templates-pack' );
		$this->slug = 'tshirt-order';
		$this->data = array(
			'field_id' => '10',
			'fields'   => array(
				1 => array(
					'id'            => '1',
					'type'          => 'divider',
					'label'         => esc_html__( 'T-Shirt Details', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'select',
					'label'    => esc_html__( 'Design', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Ninja Shirt', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Robot Shirt', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Pirate Shirt', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'       => '3',
					'type'     => 'select',
					'label'    => esc_html__( 'Size', 'wpforms-form-templates-pack' ),
					'choices'  => array(
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
					'required' => '1',
					'size'     => 'medium',
				),
				4 => array(
					'id'       => '4',
					'type'     => 'select',
					'label'    => esc_html__( 'Color', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Red', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Blue', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Green', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Black', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'White', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
					'size'     => 'medium',
				),
				5 => array(
					'id'            => '5',
					'type'          => 'divider',
					'label'         => esc_html__( 'Your Details', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				6 => array(
					'id'       => '6',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				7 => array(
					'id'       => '7',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				8 => array(
					'id'       => '8',
					'type'     => 'address',
					'label'    => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				9 => array(
					'id'         => '9',
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Gift', 'wpforms-form-templates-pack' ),
					'choices'    => array(
						1 => array(
							'label' => esc_html__( 'This is a gift', 'wpforms-form-templates-pack' ),
						),
					),
					'label_hide' => '1',
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

new WPForms_Template_Tshirt_Order;
