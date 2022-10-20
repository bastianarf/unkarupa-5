<?php


namespace Uncanny_Automator_Pro;


use Uncanny_Automator\Lifterlms_Helpers;

/**
 * Class Lifterlms_Pro_Helpers
 * @package Uncanny_Automator_Pro
 */
class Lifterlms_Pro_Helpers extends Lifterlms_Helpers {
	/**
	 * Lifterlms_Pro_Helpers constructor.
	 */
	public function __construct() {
		// Selectively load options
		if ( property_exists( '\Uncanny_Automator\Lifterlms_Helpers', 'load_options' ) ) {
			global $uncanny_automator;
			$this->load_options = $uncanny_automator->helpers->recipe->maybe_load_trigger_options( __CLASS__ );
		}

	}

	/**
	 * @param Lifterlms_Pro_Helpers $pro
	 */
	public function setPro( Lifterlms_Pro_Helpers $pro ) {
		parent::setPro( $pro );
	}
}