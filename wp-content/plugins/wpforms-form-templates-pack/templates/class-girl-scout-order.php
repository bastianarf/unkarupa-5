<?php

/**
 * Girl Scout Cookie Order form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Girl_Scout_Cookie_Order extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Girl Scout Cookie Order Form', 'wpforms-form-templates-pack' );
		$this->slug = 'girl-scout-cookie-order';
		$this->data = array(
			'field_id' => 8,
			'fields'   => array(
				1 => array(
					'id'      => '1',
					'type'    => 'select',
					'label'   => esc_html__( 'How many boxes of Samoas would you like?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						5 => array(
							'label' => '0',
						),
						1 => array(
							'label' => esc_html__( '1 - $5', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '2 - $10', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '3 - $15', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				2 => array(
					'id'      => '2',
					'type'    => 'select',
					'label'   => esc_html__( 'How many boxes of Thin Mints would you like?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => '0',
						),
						2 => array(
							'label' => esc_html__( '1 - $5', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '2 - $10', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( '3 - $15', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				3 => array(
					'id'      => '3',
					'type'    => 'select',
					'label'   => esc_html__( 'How many boxes of Tagalongs would you like?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => '0',
						),
						2 => array(
							'label' => esc_html__( '1 - $5', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '2 - $10', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( '3 - $15', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				4 => array(
					'id'       => '4',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				5 => array(
					'id'       => '5',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				6 => array(
					'id'     => '6',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				7 => array(
					'id'     => '7',
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

new WPForms_Template_Girl_Scout_Cookie_Order;
