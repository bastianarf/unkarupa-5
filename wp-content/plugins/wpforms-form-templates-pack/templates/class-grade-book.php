<?php

/**
 * Grade Book Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Grade_Book extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Grade Book Form', 'wpforms-form-templates-pack' );
		$this->slug = 'grade-book';
		$this->data = array(
			'field_id' => 7,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Student Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'    => '2',
					'type'  => 'text',
					'label' => esc_html__( 'Student ID', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Assignment name', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'      => '4',
					'type'    => 'select',
					'label'   => esc_html__( 'Status', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Complete', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Late', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Incomplete', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				5 => array(
					'id'      => '5',
					'type'    => 'select',
					'label'   => esc_html__( 'Grade', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'A', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'B', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'C', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'D', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'F', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				6 => array(
					'id'    => '6',
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

new WPForms_Template_Grade_Book;
