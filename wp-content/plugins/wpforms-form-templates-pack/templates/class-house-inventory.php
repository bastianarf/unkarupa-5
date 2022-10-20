<?php

/**
 * House Inventory Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_House_Inventory extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'House Inventory Form', 'wpforms-form-templates-pack' );
		$this->slug = 'house-inventory';
		$this->data = array(
			'field_id' => 10,
			'fields'   => array(
				1 => array(
					'id'    => '1',
					'type'  => 'text',
					'label' => esc_html__( 'Item', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2 => array(
					'id'    => '2',
					'type'  => 'textarea',
					'label' => esc_html__( 'Description', 'wpforms-form-templates-pack' ),
					'size'  => 'small',
				),
				3 => array(
					'id'     => '3',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Appraised Value', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				4 => array(
					'id'            => '4',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date Purchased', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'dropdown',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				9 => array(
					'id'    => '9',
					'type'  => 'text',
					'label' => esc_html__( 'Manufacturer', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'text',
					'label' => esc_html__( 'Model', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'Model Number', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7 => array(
					'id'      => '7',
					'type'    => 'select',
					'label'   => esc_html__( 'Condition', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Like New', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Used', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Poor', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
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

new WPForms_Template_House_Inventory;
