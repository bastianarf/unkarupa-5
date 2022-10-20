<?php

/**
 * Potluck Invitation RSVP Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Potluck_Invitation_RSVP extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Potluck Invitation RSVP Form', 'wpforms-form-templates-pack' );
		$this->slug = 'potluck-invitation-rsvp';
		$this->data = array(
			'field_id' => 5,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'      => '2',
					'type'    => 'radio',
					'label'   => esc_html__( 'Are you coming?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Yes, with a guest!', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'I\'m not sure yet', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Can\'t make it', 'wpforms-form-templates-pack' ),
						),
					),
				),
				3 => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'What are you bringing?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'textarea',
					'label' => esc_html__( 'Comments', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Potluck_Invitation_RSVP;
