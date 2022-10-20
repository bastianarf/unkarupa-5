<?php

/**
 * Wedding Invitation RSVP Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Wedding_Invitation_RSVP extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Wedding Invitation RSVP Form', 'wpforms-form-templates-pack' );
		$this->slug = 'wedding-invitation-rsvp';
		$this->data = array(
			'field_id' => 9,
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
					'label'    => esc_html__( 'Are you attending?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'No', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				4 => array(
					'id'      => '4',
					'type'    => 'radio',
					'label'   => esc_html__( 'Your meal selection', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Chicken', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Fish', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Salad', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5 => array(
					'id'       => '5',
					'type'     => 'radio',
					'label'    => esc_html__( 'Are you bringing a guest?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'No', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				6 => array(
					'id'       => '6',
					'type'     => 'name',
					'label'    => esc_html__( 'Guest Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				7 => array(
					'id'      => '7',
					'type'    => 'radio',
					'label'   => esc_html__( 'Guest meal selection', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Chicken', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Fish', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Salad', 'wpforms-form-templates-pack' ),
						),
					),
				),
				8 => array(
					'id'    => '8',
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

new WPForms_Template_Wedding_Invitation_RSVP;
