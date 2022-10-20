<?php

/**
 * Video Release Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Video_Release extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Video Release Form', 'wpforms-form-templates-pack' );
		$this->slug = 'video-release';
		$this->data = array(
			'field_id' => 5,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name of Individual Being Recorded', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'            => '2',
					'type'          => 'divider',
					'label'         => esc_html__( 'Conditions', 'wpforms-form-templates-pack' ),
					'description'   => wp_kses(
						__( '<em>(Please note, this example form does not constitute a fully written and usable photography release. The content here is merely for example purposes.</em>
							<p>I hereby grant the XYZ COMPANY, its directors, officers, employees, agents, and designers (collectively “XYZ COMPANY”) non-revocable permission to capture my image and likeness in videotapes, motion pictures, recordings, or any other media (collectively “Images”). I acknowledge that XYZ COMPANY will own such Images and further grant the XYZ COMPANY permission to copyright, display, publish, distribute, use, modify, print and reprint such Images in any manner whatsoever related to XYZ COMPANY business, including without limitation, publications, advertisements, brochures, web site images, or other electronic displays and transmissions thereof. I further waive any right to inspect or approve the use of the Image by the XYZ COMPANY prior to its use. I forever release and hold the XYZ COMPANY harmless from any and all liability arising out of the use of the Images in any manner or media whatsoever, and waive any and all claims and causes of action relating to use of the Images, including without limitation, claims for invasion of privacy rights or publicity. </p>
							<p>I hereby warrant that I am eighteen (18) years old or more and competent to contract in my own name or, if I am less than eighteen years old, that my parent or guardian has signed this release form below. This release is binding on me and my heirs, assignees and personal representatives.</p>', 'wpforms-form-templates-pack' ),
						array(
							'em' => array(),
							'p'  => array(),
						)
					),
					'label_disable' => '1',
				),
				3 => array(
					'id'            => '3',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				4 => array(
					'id'         => '4',
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Agreement', 'wpforms-form-templates-pack' ),
					'choices'    => array(
						1 => array(
							'label' => esc_html__( 'I agree to the conditions above.', 'wpforms-form-templates-pack' ),
						),
					),
					'required'   => '1',
					'label_hide' => '1',
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

new WPForms_Template_Video_Release;
