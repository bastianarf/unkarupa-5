<?php

/**
 * Support Ticket Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Support_Ticket extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Support Ticket Form', 'wpforms-form-templates-pack' );
		$this->slug = 'support-ticket';
		$this->data = array(
			'field_id' => 7,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
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
					'id'      => '4',
					'type'    => 'radio',
					'label'   => esc_html__( 'What can we help you with today?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'General question', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Feature request', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Bug report', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'My account', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5 => array(
					'id'    => '5',
					'type'  => 'url',
					'label' => esc_html__( 'Your Website', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'Issues / Message', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Support_Ticket;
