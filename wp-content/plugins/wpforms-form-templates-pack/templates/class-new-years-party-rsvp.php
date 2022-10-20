<?php

/**
 * New Years Party RSVP Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_New_Years_Party_RSVP extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'New Years Party RSVP Form', 'wpforms-form-templates-pack' );
		$this->slug = 'new-years-party-rsvp';
		$this->data = array(
			'field_id' => 6,
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
					'label'    => esc_html__( 'Can you make it?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'No', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Not sure', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				3 => array(
					'id'      => '3',
					'type'    => 'radio',
					'label'   => esc_html__( 'How many people will be joining?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Just me', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Bringing a friend', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Showing up with the group', 'wpforms-form-templates-pack' ),
						),
					),
				),
				4 => array(
					'id'      => '4',
					'type'    => 'radio',
					'label'   => esc_html__( 'Are you bringing anything else?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Snacks', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Dessert', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Bubbly beverages', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5 => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'Anything else we should know?', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_New_Years_Party_RSVP;
