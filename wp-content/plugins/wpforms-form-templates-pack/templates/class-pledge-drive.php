<?php

/**
 * Pledge Drive form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Pledge_Drive extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Pledge Drive Form', 'wpforms-form-templates-pack' );
		$this->slug = 'pledge-drive';
		$this->data = array(
			'field_id' => 8,
			'fields'   => array(
				1 => array(
					'id'          => '1',
					'type'        => 'name',
					'label'       => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'      => 'first-last',
					'required'    => '1',
					'size'        => 'medium',
				),
				4 => array(
					'id'       => '4',
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
				2 => array(
					'id'     => '2',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				5 => array(
					'id'       => '5',
					'type'     => 'radio',
					'label'    => esc_html__( 'I am pledging support to this campaign as a/an :', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Student: $20', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Individual: $45', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Family: $60', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Sustainer: $150', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Friend: $300', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Patron: $500', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Benefactor: $1000', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'In Memory Of', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'text',
					'label' => esc_html__( 'In Honor Of', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Pledge_Drive;
