<?php

/**
 * Lecture Notes Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Lecture_Notes extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Lecture Notes Form', 'wpforms-form-templates-pack' );
		$this->slug = 'lecture-notes';
		$this->data = array(
			'field_id' => 5,
			'fields'   => array(
				1 => array(
					'id'    => '1',
					'type'  => 'text',
					'label' => esc_html__( 'Subject', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2 => array(
					'id'    => '2',
					'type'  => 'textarea',
					'label' => esc_html__( 'Cues', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'    => '3',
					'type'  => 'textarea',
					'label' => esc_html__( 'Notes', 'wpforms-form-templates-pack' ),
					'size'  => 'large',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'textarea',
					'label' => esc_html__( 'Summary', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Lecture_Notes;
