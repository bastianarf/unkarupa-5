<?php

/**
 * Equipment Checkout Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Equipment_Checkout extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Equipment Checkout Form', 'wpforms-form-templates-pack' );
		$this->slug = 'equipment-checkout';
		$this->data = array(
			'field_id' => 7,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Employee Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'text',
					'label'    => esc_html__( 'Employee ID', 'wpforms-form-templates-pack' ),
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
				4 => array(
					'id'     => '4',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				5 => array(
					'id'      => '5',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Equipment Checked Out', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Laptop', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Laptop Carrying Case', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'AC Power Cord', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Docking Station', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Bluetooth Mouse', 'wpforms-form-templates-pack' ),
						),
					),
				),
				6 => array(
					'id'         => '6',
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Agreement', 'wpforms-form-templates-pack' ),
					'choices'    => array(
						1 => array(
							'label' => esc_html__( 'I acknowledge the equipment I have received is in good physical working condition and I will return the equipment in the same condition. I take financial responsibility for any damages to the equipment upon return.', 'wpforms-form-templates-pack' ),
						),
					),
					'required'   => '1',
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

new WPForms_Template_Equipment_Checkout;
