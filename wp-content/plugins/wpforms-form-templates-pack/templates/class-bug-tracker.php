<?php

/**
 * Bug Tracker Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Bug_Tracker extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Bug Tracker Form', 'wpforms-form-templates-pack' );
		$this->slug = 'bug-tracker';
		$this->data = array(
			'field_id' => 10,
			'fields'   => array(
				8 => array(
					'id'       => '8',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				9 => array(
					'id'       => '9',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'    => '2',
					'type'  => 'textarea',
					'label' => esc_html__( 'Describe what happened or steps to reproduce the issue', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'      => '3',
					'type'    => 'select',
					'label'   => esc_html__( 'Operating System', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Windows 7', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Windows 8', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Windows 10', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'macOS', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Linux', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'iOS', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Android', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				4 => array(
					'id'      => '4',
					'type'    => 'select',
					'label'   => esc_html__( 'Browser', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Chrome', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Firefox', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Opera', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Edge', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Internet Explorer', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Safari', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				6 => array(
					'id'      => '6',
					'type'    => 'select',
					'label'   => esc_html__( 'Severity', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Low', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Normal', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'High', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'file-upload',
					'label' => esc_html__( 'Upload a screenshot', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Bug_Tracker;
