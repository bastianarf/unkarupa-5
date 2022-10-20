<?php

/**
 * Contest Entry Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Contest_Entry extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Contest Entry Form', 'wpforms-form-templates-pack' );
		$this->slug = 'contest-entry';
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
					'id'     => '3',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				4 => array(
					'id'     => '4',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				5 => array(
					'id'            => '5',
					'type'          => 'html',
					'code'          => '<strong>Rules of Entry</strong>
		<p>1. To participate, complete an entry form with your name, address, and phone number, and submit it online.</p>
		<p>2. The contest will be conducted March 17th through April 28th, 2017. Deadline for entries is April 28th, 2017. Grand prize drawing will be held on Wednesday, April 28th at the Convention Center.</p>
		<p>3. You must be 21 years of age or older to win.	No purchase necessary.	One qualifier per household. You need not be present to win. Void where prohibited.</p>
		<p>4. Approximate value of the prize is $1000.</p>',
					'label_disable' => '1',
				),
				6 => array(
					'id'         => '6',
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Agreement', 'wpforms-form-templates-pack' ),
					'choices'    => array(
						1 => array(
							'label' => esc_html__( 'I agree to the Rules of Entry.', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Contest_Entry;
