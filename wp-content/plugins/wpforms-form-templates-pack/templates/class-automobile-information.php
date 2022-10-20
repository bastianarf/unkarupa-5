<?php

/**
 * Automobile Information Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Automobile_Information extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Automobile Information Form', 'wpforms-form-templates-pack' );
		$this->slug = 'automobile-information';
		$this->data = array(
			'field_id' => 7,
			'fields'   => array(
				1 => array(
					'id'    => '1',
					'type'  => 'number',
					'label' => esc_html__( 'Year', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2 => array(
					'id'    => '2',
					'type'  => 'text',
					'label' => esc_html__( 'Make (eg Ford)', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Model (eg Mustang)', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'text',
					'label' => esc_html__( 'What is your automobile\'s vehicle identification number (VIN)?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'number',
					'label' => esc_html__( 'Since you bought or leased your automobile, about how many miles has it been driven?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'number',
					'label' => esc_html__( 'About how many miles is your automobile driven in a typical week ?', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Automobile_Information;
