<?php
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class Extensions_Manager {

    public static function init() {
		include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/happy-features.php';

		if ( hapro_is_image_masking_enabled() ) {
			include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/image-masking.php';
		}

		if ( hapro_is_happy_particle_effects_enabled() ) {
			include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/happy-particle-effects.php';
		}
	}

	public static function load_display_condition() {
		include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/display-conditions.php';
	    include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/conditions/condition.php';
	}
}

Extensions_Manager::init();
