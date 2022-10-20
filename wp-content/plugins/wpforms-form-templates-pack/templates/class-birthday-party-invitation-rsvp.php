<?php

/**
 * Birthday Party Invitation RSVP Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Birthday_Party_Invitation_RSVP extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Birthday Party Invitation RSVP Form', 'wpforms-form-templates-pack' );
		$this->slug = 'birthday-party-invitation-rsvp';
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
					'label'   => esc_html__( 'Will you be able to make it?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Yeah!', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Maybe / Not sure', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'I can\'t', 'wpforms-form-templates-pack' ),
						),
					),
				),
				3 => array(
					'id'      => '3',
					'type'    => 'select',
					'label'   => esc_html__( 'How many people are you going to bring?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Just me!', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Plus one', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'A few friends', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'textarea',
					'label' => esc_html__( 'Comments or Questions', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Birthday_Party_Invitation_RSVP();
