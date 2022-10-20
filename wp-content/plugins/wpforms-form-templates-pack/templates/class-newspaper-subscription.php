<?php

/**
 * Newspaper form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Newspaper_Subscription extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Newspaper Subscription Form', 'wpforms-form-templates-pack' );
		$this->slug = 'newspaper-subscription';
		$this->data = array(
			'field_id' => '4',
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'radio',
					'label'    => esc_html__( 'How often would you like the paper?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Daily: $150', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Weekend (Thurs-Sun): $100', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Sunday Only: $50', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'       => '3',
					'type'     => 'address',
					'label'    => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme'   => 'us',
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

new WPForms_Template_Newspaper_Subscription;
