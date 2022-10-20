<?php

/**
 * Online Race Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Online_Race_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Online Race Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'online-race-registration';
		$this->data = array(
			'field_id' => 11,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'       => '2',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				3  => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Company (if applicable)', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9  => array(
					'id'      => '9',
					'type'    => 'select',
					'label'   => esc_html__( 'Dropdown', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( '5k', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Half marathon', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Marathon', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				4  => array(
					'id'      => '4',
					'type'    => 'radio',
					'label'   => esc_html__( 'Running speed', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Slower', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Moderate', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Faster', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5  => array(
					'id'      => '5',
					'type'    => 'radio',
					'label'   => esc_html__( 'Shirt size', 'wpforms-form-templates-pack' ),
					'choices' => array(
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
				),
				6  => array(
					'id'      => '6',
					'type'    => 'radio',
					'label'   => esc_html__( 'How did you hear about this event?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Word of mouth', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Participated in the past', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Advertisement', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Social Media', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7  => array(
					'id'    => '7',
					'type'  => 'text',
					'label' => esc_html__( 'If other', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				10 => array(
					'id'    => '10',
					'type'  => 'textarea',
					'label' => esc_html__( 'Comments or Requests', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Online_Race_Registration;
