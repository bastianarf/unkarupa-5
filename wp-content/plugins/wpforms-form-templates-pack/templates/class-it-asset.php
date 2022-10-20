<?php

/**
 * IT Asset Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_IT_Asset extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'IT Asset Form', 'wpforms-form-templates-pack' );
		$this->slug = 'it-asset';
		$this->data = array(
			'field_id' => 8,
			'fields'   => array(
				1 => array(
					'id'      => '1',
					'type'    => 'select',
					'label'   => esc_html__( 'Asset Category', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Building Key/Keycard', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Cell phone', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Desktop Computer', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Notebook Computer', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Keyboard', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Mouse', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Monitor', 'wpforms-form-templates-pack' ),
						),
						8 => array(
							'label' => esc_html__( 'Printer', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'name',
					'label'    => esc_html__( 'Asset Owner Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Asset Description', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'text',
					'label' => esc_html__( 'Serial #', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'text',
					'label' => esc_html__( 'Model #', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'     => '6',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Asset Cost', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'text',
					'label' => esc_html__( 'Condition', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_IT_Asset;
