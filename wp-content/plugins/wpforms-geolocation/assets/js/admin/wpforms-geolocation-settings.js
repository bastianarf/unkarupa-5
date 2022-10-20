'use strict';

/**
 * WPForms Geolocation Settings functions.
 *
 * @since 2.0.0
 */
var WPFormsSettingsGeolocation = window.WPFormsSettingsGeolocation || ( function( document, window, $ ) {

	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 2.0.0
		 */
		init: function() {

			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 2.0.0
		 */
		ready: function() {

			app.bindEvents();
		},

		/**
		 * Bind events.
		 *
		 * @since 2.0.0
		 */
		bindEvents: function() {

			var $providerField = $( 'input[name="geolocation-field-provider"]', '#wpforms-setting-row-geolocation-field-provider' ),
				$locationField = $( '#wpforms-setting-row-geolocation-current-location' ),
				$providerFields = $( '.wpforms-geolocation-settings-provider' );

			$providerField.on( 'change', function() {

				var provider = $( this ).val();

				$providerFields.hide();

				if ( ! provider ) {
					$locationField.hide();

					return;
				}

				var $activeProviders = $( '.wpforms-geolocation-settings-provider-' + provider );

				$locationField.show();
				$activeProviders.show();
			} );
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsSettingsGeolocation.init();
