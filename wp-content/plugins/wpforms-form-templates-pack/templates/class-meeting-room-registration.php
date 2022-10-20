<?php

/**
 * Meeting Room Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Meeting_Room_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Meeting Room Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'meeting-room-registration';
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
				2 => array(
					'id'       => '2',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Department', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'       => '4',
					'type'     => 'radio',
					'label'    => esc_html__( 'Which room would you like to reserve?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Room A', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Room B', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Room C', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				5 => array(
					'id'       => '5',
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Which time blocks would you like to reserve?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( '8:00 - 9:00am', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '9:00 - 10:00am', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '10:00 - 11:00am', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( '11:00 - 12:00pm', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( '12:00 - 1:00pm', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( '1:00 - 2:00pm', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( '2:00 - 3:00pm', 'wpforms-form-templates-pack' ),
						),
						8 => array(
							'label' => esc_html__( '3:00 - 4:00pm', 'wpforms-form-templates-pack' ),
						),
						9 => array(
							'label' => esc_html__( '4:00 - 5:00pm', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'Questions or Comments', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Meeting_Room_Registration;
