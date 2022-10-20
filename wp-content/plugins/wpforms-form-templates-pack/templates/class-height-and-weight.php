<?php

/**
 * Height and Weight Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Height_And_Weight extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Height and Weight Form', 'wpforms-form-templates-pack' );
		$this->slug = 'height-and-weight';
		$this->data = array(
			'field_id' => 5,
			'fields'   => array(
				1 => array(
					'id'            => '1',
					'type'          => 'divider',
					'description'   => esc_html__( 'What is your height in feet and inches? (Remove shoes before measuring.)', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				2 => array(
					'id'    => '2',
					'type'  => 'text',
					'label' => esc_html__( 'Feet', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Inches', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'text',
					'label' => esc_html__( 'What is your current weight in pounds?', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Height_And_Weight;
