<?php

/**
 * Job Application Upload Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Job_Application_Upload extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Job Application Upload Form', 'wpforms-form-templates-pack' );
		$this->slug = 'job-application-upload';
		$this->data = array(
			'field_id' => 11,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'       => '2',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				4  => array(
					'id'       => '4',
					'type'     => 'phone',
					'label'    => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				5  => array(
					'id'       => '5',
					'type'     => 'address',
					'label'    => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				6  => array(
					'id'      => '6',
					'type'    => 'radio',
					'label'   => esc_html__( 'How did you find out about this position?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Current Employee', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Career Fair', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Newspaper Ad', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Radio/TV AD', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Search Engine', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Social Media', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Craigslist', 'wpforms-form-templates-pack' ),
						),
						8 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7  => array(
					'id'    => '7',
					'type'  => 'text',
					'label' => esc_html__( 'If other', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8  => array(
					'id'       => '8',
					'type'     => 'file-upload',
					'label'    => esc_html__( 'Upload your resume', 'wpforms-form-templates-pack' ),
					'required' => '1',
				),
				9  => array(
					'id'    => '9',
					'type'  => 'file-upload',
					'label' => esc_html__( 'Upload a cover letter', 'wpforms-form-templates-pack' ),
				),
				10 => array(
					'id'    => '10',
					'type'  => 'textarea',
					'label' => esc_html__( 'Additional Information', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Job_Application_Upload;
