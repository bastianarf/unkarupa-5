<?php

namespace WPFormsGeolocation;

/**
 * Class Integrations.
 *
 * @since 2.0.0
 */
class Integrations {

	/**
	 * Init hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_action( 'admin_enqueue_scripts', [ $this, 'gutenberg_enqueue_styles' ] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'elementor_enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'divi_enqueue_styles' ] );
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 2.0.0
	 */
	private function enqueue_styles() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-geolocation-admin',
			WPFORMS_GEOLOCATION_URL . "assets/css/admin/wpforms-geolocation-admin{$min}.css",
			[],
			WPFORMS_GEOLOCATION_VERSION
		);
	}

	/**
	 * Enqueue styles for the Gutenberg editor.
	 *
	 * @since 2.0.0
	 */
	public function gutenberg_enqueue_styles() {

		$screen = get_current_screen();

		if ( ! $screen || ! method_exists( $screen, 'is_block_editor' ) || ! $screen->is_block_editor() ) {
			return;
		}

		$this->enqueue_styles();
	}

	/**
	 * Enqueue styles for the Elementor Builder.
	 *
	 * @since 2.0.0
	 */
	public function elementor_enqueue_styles() {

		if ( ! class_exists( '\Elementor\Plugin' ) || ! \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
			return;
		}

		$this->enqueue_styles();
	}

	/**
	 * Enqueue styles for the Divi Builder.
	 *
	 * @since 2.0.0
	 */
	public function divi_enqueue_styles() {

		if ( empty( $_GET['et_fb'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$this->enqueue_styles();
	}
}
