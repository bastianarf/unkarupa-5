<?php

use WPFormsGeolocation\SmartTags;
use WPFormsGeolocation\Admin\Entry;
use WPFormsGeolocation\RetrieveGeoData;

/**
 * Geolocation.
 *
 * @since      1.0.0
 * @deprecated 2.0.0
 */
class WPForms_Geolocation {

	/**
	 * Retrieve geolocation data.
	 *
	 * @since      2.0.0
	 * @deprecated 2.0.0
	 *
	 * @var \WPFormsGeolocation\RetrieveGeoData
	 */
	private $retrieve_geo_data;

	/**
	 * Entry.
	 *
	 * @since      2.0.0
	 * @deprecated 2.0.0
	 *
	 * @var \WPFormsGeolocation\Admin\Entry
	 */
	private $entry;

	/**
	 * Smart tags.
	 *
	 * @since      2.0.0
	 * @deprecated 2.0.0
	 *
	 * @var \WPFormsGeolocation\SmartTags
	 */
	private $smart_tags;

	/**
	 * Primary class constructor.
	 *
	 * @since      1.0.0
	 * @deprecated 2.0.0
	 */
	public function __construct() {

		_deprecated_constructor( __CLASS__, '2.0.0' );
		$this->retrieve_geo_data = new RetrieveGeoData();
		$this->entry             = new Entry( $this->retrieve_geo_data );
		$this->smart_tags        = new SmartTags();

		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since      1.0.0
	 * @deprecated 2.0.0
	 */
	public function init() {

		_deprecated_function( __CLASS__ . '::' . __METHOD__, '2.0.0' );
		add_action( 'wpforms_process_entry_save', [ $this, 'entry_save_location' ], 20, 4 );
		add_action( 'wpforms_email_message', [ $this, 'entry_location_smarttag' ], 10, 2 );
		add_action( 'wpforms_entry_details_init', [ $this, 'entry_details_init' ] );
		add_action( 'wpforms_entry_details_content', [ $this, 'entry_details_location' ], 20 );
	}

	/**
	 * Maybe fetch geolocation data.
	 *
	 * If a form is using the location smart tag in an email notification, then
	 * we need to process the geolocation data before emails are sent. Otherwise
	 * geolocation data is processed on-demand when viewing an individual entry.
	 *
	 * @since      1.0.0
	 * @deprecated 2.0.0
	 *
	 * @param array $fields    Form fields data.
	 * @param array $entry     Entry data.
	 * @param int   $form_id   Form ID.
	 * @param array $form_data Form data.
	 */
	public function entry_save_location( $fields, $entry, $form_id, $form_data ) {

		_deprecated_function( __CLASS__ . '::' . __METHOD__, '2.0.0' );

		$this->entry->entry_save_location( $fields, $entry, $form_id, $form_data );
	}

	/**
	 * Check for {entry_geolocation} Smart Tag inside email messages.
	 *
	 * @since      1.0.0
	 * @deprecated 2.0.0
	 *
	 * @param string            $message Email message.
	 * @param WPForms_WP_Emails $email   Email object.
	 *
	 * @return string
	 */
	public function entry_location_smarttag( $message, $email ) {

		_deprecated_function( __CLASS__ . '::' . __METHOD__, '2.0.0' );

		return $this->smart_tags->entry_location( $message, $email );
	}

	/**
	 * Maybe process geolocation data when an individual entry is viewed.
	 *
	 * @since      1.0.0
	 * @deprecated 2.0.0
	 *
	 * @param WPForms_Entries_Single $entries Single form entry.
	 */
	public function entry_details_init( $entries ) {

		_deprecated_function( __CLASS__ . '::' . __METHOD__, '2.0.0' );

		$this->entry->entry_details_init( $entries );
	}

	/**
	 * Entry details location metabox, display the info and make it look fancy.
	 *
	 * @since      1.0.0
	 * @deprecated 2.0.0
	 *
	 * @param object $entry Entry data.
	 */
	public function entry_details_location( $entry ) {

		_deprecated_function( __CLASS__ . '::' . __METHOD__, '2.0.0' );
		$this->entry->entry_details_location( $entry );
	}

	/**
	 * Get geolocation information from an IP address.
	 *
	 * @since      1.0.0
	 * @deprecated 2.0.0
	 *
	 * @param string $ip IP address.
	 *
	 * @return false|array False for local/incorrect IP address, array of data otherwise.
	 */
	public function get_location( $ip = '' ) {

		_deprecated_function( __CLASS__ . '::' . __METHOD__, '2.0.0' );

		return $this->retrieve_geo_data->get_location( $ip );
	}
}

new WPForms_Geolocation();
