<?php

/**
 * Simple RSVP Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Simple_RSVP extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Simple RSVP Form', 'wpforms-form-templates-pack' );
		$this->slug = 'simple-rsvp';
		$this->data = array(
			'field_id' => 8,
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
					'label'    => esc_html__( 'Are you able to attend?', 'wpforms-form-templates-pack' ),
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
					'id'    => '3',
					'type'  => 'number',
					'label' => esc_html__( 'How many guests will you be bringing?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'      => '4',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Do you or your guest(s) have any dietary restrictions?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						5 => array(
							'label' => esc_html__( 'None', 'wpforms-form-templates-pack' ),
						),
						1 => array(
							'label' => esc_html__( 'Vegetarian', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Vegan', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Gluten-free', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Dairy-free', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7 => array(
					'id'    => '7',
					'type'  => 'text',
					'label' => esc_html__( 'If other', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'Please elaborate on any dietary restrictions that apply, or let us know if there\'s anything else you\'d like to tell us. Hope to see you soon', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Simple_RSVP;
