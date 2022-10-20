<?php

/**
 * Lesson Plan Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Lesson_Plan extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Lesson Plan Form', 'wpforms-form-templates-pack' );
		$this->slug = 'lesson-plan';
		$this->data = array(
			'field_id' => 11,
			'fields'   => array(
				1  => array(
					'id'    => '1',
					'type'  => 'text',
					'label' => esc_html__( 'Subject', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2  => array(
					'id'    => '2',
					'type'  => 'text',
					'label' => esc_html__( 'Teaching Topic', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3  => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Lesson Plan Title', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4  => array(
					'id'      => '4',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Standards Addressed', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'First Choice', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Second Choice', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Third Choice', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5  => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'Goals/Objectives of Lesson Plans', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6  => array(
					'id'      => '6',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Required Materials', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Photos', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Electronic Devices', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Paper and Pencil', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Audio', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7  => array(
					'id'    => '7',
					'type'  => 'textarea',
					'label' => esc_html__( 'Step-by-Step Procedure', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8  => array(
					'id'    => '8',
					'type'  => 'textarea',
					'label' => esc_html__( 'Additional Comments', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9  => array(
					'id'       => '9',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				10 => array(
					'id'       => '10',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
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

new WPForms_Template_Lesson_Plan;
