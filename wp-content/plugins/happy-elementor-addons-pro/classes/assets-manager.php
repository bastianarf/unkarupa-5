<?php
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class Assets_Manager {

	/**
	 * Bind hook and run internal methods here
	 */
	public static function init() {
	

		// Frontend scripts
		// Do not delete the "0" priority
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'frontend_register' ], 0 );
		add_action( 'happyaddons_enqueue_assets', [ __CLASS__, 'frontend_enqueue' ] );

		add_filter( 'happyaddons_get_styles_file_path', [ __CLASS__, 'set_styles_file_path' ], 10, 3 );

		add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'enqueue_editor_scripts' ] );

		add_action( 'elementor/preview/enqueue_scripts', [ __CLASS__, 'preview_enqueue' ] );
	}

	public static function set_styles_file_path( $file_path, $file_name, $is_pro ) {
		if ( $is_pro ) {
			return sprintf(
				'%1$sassets/css/widgets/%2$s.min.css',
				HAPPY_ADDONS_PRO_DIR_PATH,
				$file_name
			);
		}

		if ( $file_name === Widgets_Manager::COMMON_WIDGET_KEY ) {
			return sprintf(
				'%1$sassets/css/widgets/%2$s.min.css',
				HAPPY_ADDONS_PRO_DIR_PATH,
				'common'
			);
		}

		return $file_path;
	}

	public static function frontend_register() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '';

		// Prism
		wp_register_style(
			'prism',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/prism/css/prism.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_script(
			'prism',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/prism/js/prism.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		//Countdown
		// Unregister first to load our own countdown version
		wp_deregister_script( 'jquery-countdown' );
		wp_register_script(
			'jquery-countdown',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/countdown/js/countdown' . $suffix . '.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		//Animated Text
		wp_register_script(
			'animated-text',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/animated-text/js/animated-text.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		//Keyframes
		wp_register_script(
			'jquery-keyframes',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/keyframes/js/jquery.keyframes.min.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		// Tipso: tooltip plugin
		wp_register_style(
			'tipso',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/tipso/tipso' . $suffix . '.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		// animate.css
		wp_register_style(
			'animate-css',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/animate-css/main.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_script(
			'jquery-tipso',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/tipso/tipso' . $suffix . '.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		// Chart.js
		wp_register_script(
			'chart-js',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/chart/chart.min.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		// datatables.js
		wp_register_script(
			'data-table',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/data-table/datatables.min.js',
			['jquery'],
			HAPPY_ADDONS_PRO_VERSION
		);

		// Nice Select Plugin
		wp_register_style(
			'nice-select',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/nice-select/nice-select.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_script(
			'jquery-nice-select',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/nice-select/jquery.nice-select.min.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		// Plyr: video player plugin
		wp_register_style(
			'plyr',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/plyr/plyr.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_script(
			'plyr',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/plyr/plyr.min.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		// HappyAddons Pro
		wp_register_style(
			'happy-addons-pro',
			HAPPY_ADDONS_PRO_ASSETS . 'css/main' . $suffix . '.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_script(
			'happy-addons-pro',
			HAPPY_ADDONS_PRO_ASSETS . 'js/happy-addons-pro' . $suffix . '.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		//Localize scripts
		wp_localize_script( 'happy-addons-pro', 'HappyProLocalize', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'happy_addons_pro_nonce' ),
			'ha_particles_url' => HAPPY_ADDONS_PRO_ASSETS . 'vendor/particles/particles.js',
		] );
	}

	public static function enqueue_editor_scripts() {
		wp_enqueue_script(
			'happy-addons-pro-editor',
			HAPPY_ADDONS_PRO_ASSETS . 'admin/js/editor.min.js',
			[ 'elementor-editor' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);
	}

	public static function frontend_enqueue( $is_cache ) {
		if ( ! $is_cache ) {
			wp_enqueue_style( 'happy-addons-pro' );
			wp_enqueue_script( 'happy-addons-pro' );
		} else {
			wp_enqueue_script( 'happy-addons-pro' );
		}
	}

	public static function preview_enqueue() {
		wp_enqueue_script(
			'happy-addons-preview',
			HAPPY_ADDONS_PRO_ASSETS . 'admin/js/preview.min.js',
			[ 'elementor-frontend' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);
	}
}

Assets_Manager::init();
