<?php

namespace WPFormsGeolocation\Tasks;

use WPForms\Tasks\Task;
use WPForms\Tasks\Meta;
use WPFormsGeolocation\RetrieveGeoData;

/**
 * Class EntryGeolocationUpdateTask.
 *
 * @since 2.0.0
 */
class EntryGeolocationUpdateTask extends Task {

	/**
	 * Action name for this task.
	 *
	 * @since 2.0.0
	 */
	const ACTION = 'wpforms_geolocation_update';

	/**
	 * Retrieve Geo Data.
	 *
	 * @since 2.0.0
	 *
	 * @var RetrieveGeoData
	 */
	private $retrieve_geo_data;

	/**
	 * Class constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param RetrieveGeoData $retrieve_geo_data Retrieve Geo Data.
	 */
	public function __construct( RetrieveGeoData $retrieve_geo_data ) {

		$this->retrieve_geo_data = $retrieve_geo_data;

		parent::__construct( self::ACTION );
	}

	/**
	 * Hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_action( self::ACTION, [ $this, 'process' ] );
	}

	/**
	 * Get the data from Tasks meta table, check/unpack it and
	 * send the email straight away.
	 *
	 * @since 2.0.0
	 *
	 * @param int $meta_id ID for meta information for a task.
	 */
	public function process( $meta_id ) {

		$task_meta = new Meta();
		$meta      = $task_meta->get( (int) $meta_id );

		// We should actually receive something.
		if ( empty( $meta ) || empty( $meta->data ) ) {
			return;
		}

		// We expect a certain number of params.
		if ( count( $meta->data ) !== 3 ) {
			return;
		}

		// We expect a certain meta data structure for this task.
		list( $entry_id, $form_id, $ip ) = $meta->data;

		$location = wpforms()->entry_meta->get_meta(
			[
				'entry_id' => $entry_id,
				'type'     => 'location',
				'number'   => 1,
			]
		);

		if ( ! empty( $location ) ) {
			return;
		}

		$location = $this->retrieve_geo_data->get_location( $ip );

		if ( $location ) {
			$data = [
				'entry_id' => absint( $entry_id ),
				'form_id'  => absint( $form_id ),
				'type'     => 'location',
				'data'     => wp_json_encode( $location ),
			];

			wpforms()->entry_meta->add( $data, 'entry_meta' );
		}
	}
}
