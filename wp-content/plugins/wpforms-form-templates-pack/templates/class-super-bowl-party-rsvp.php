<?php

/**
 * Super Bowl Party RSVP Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Super_Bowl_Party_RSVP extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Super Bowl Party RSVP Form', 'wpforms-form-templates-pack' );
		$this->slug = 'super-bowl-party-rsvp';
		$this->data = array(
			'field_id' => 10,
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
					'id'       => '2',
					'type'     => 'radio',
					'label'    => esc_html__( 'Will you make it?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Yes, I\'ll be there', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'No, I can\'t handle it', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'text',
					'label' => esc_html__( 'You\'re bringing...', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'number',
					'label' => esc_html__( 'Any guests?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'text',
					'label' => esc_html__( 'The winning team will be', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8 => array(
					'id'    => '8',
					'type'  => 'text',
					'label' => esc_html__( 'Guess the final score', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Super_Bowl_Party_RSVP;
