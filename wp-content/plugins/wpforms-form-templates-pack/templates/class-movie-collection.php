<?php

/**
 * Movie Collection Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Movie_Collection extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Movie Collection Form', 'wpforms-form-templates-pack' );
		$this->slug = 'movie-collection';
		$this->data = array(
			'field_id' => 10,
			'fields'   => array(
				1 => array(
					'id'    => '1',
					'type'  => 'text',
					'label' => esc_html__( 'Title', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2 => array(
					'id'    => '2',
					'type'  => 'text',
					'label' => esc_html__( 'Director', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Producer', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'            => '4',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Release Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'text',
					'label' => esc_html__( 'Length', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'Actors', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'textarea',
					'label' => esc_html__( 'Plot', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8 => array(
					'id'    => '8',
					'type'  => 'textarea',
					'label' => esc_html__( 'Review', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9 => array(
					'id'      => '9',
					'type'    => 'select',
					'label'   => esc_html__( 'Rating', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( '1', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '2', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '3', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( '4', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( '5', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
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

new WPForms_Template_Movie_Collection;
