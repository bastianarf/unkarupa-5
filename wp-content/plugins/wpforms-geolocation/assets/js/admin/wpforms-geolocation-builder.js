/* global wpforms_builder */

'use strict';

/**
 * WPForms Builder Geolocation functions.
 *
 * @since 2.0.0
 */
var WPFormsBuilderGeolocation = window.WPFormsBuilderGeolocation || ( function( document, window, $ ) {

	/**
	 * Builder element.
	 *
	 * @since 2.0.0
	 */
	var $builder;

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

			$builder = $( '#wpforms-builder' );
			app.bindEvents();
			app.loadDefaultState();
		},

		/**
		 * Bind events.
		 *
		 * @since 2.0.0
		 */
		bindEvents: function() {

			$builder
				.on(
					'click',
					'.wpforms-field-option-row-enable_address_autocomplete.wpforms-geolocation-fill-settings input',
					app.autocompleteRequire,
				)
				.on(
					'change',
					'.wpforms-field-option-row-enable_address_autocomplete:not(.wpforms-geolocation-fill-settings) input',
					app.toggleAutocompleteOptions,
				)
				.on(
					'change',
					'.wpforms-field-option-row-enable_address_autocomplete:not(.wpforms-geolocation-fill-settings) input',
					app.toggleLimitLength,
				)
				.on( 'change', '.wpforms-field-option-row-display_map input', app.toggleMapDisplay )
				.on( 'change', '.wpforms-field-option-row-map_position select', app.changeMapPosition );
		},

		/**
		 * Load map preview for fields.
		 *
		 * @since 2.0.0
		 */
		loadDefaultState: function() {

			$( '.wpforms-field-option-row-enable_address_autocomplete input,.wpforms-field-option-row-display_map input' ).each( function() {
				$( this ).trigger( 'change' );
			} );
		},

		/**
		 * Check autocomplete require.
		 *
		 * @since 2.0.0
		 */
		autocompleteRequire: function() {

			if ( $( this ).prop( 'checked' ) ) {
				app.fillPlacesAPI();
				$( this ).removeAttr( 'checked' );
			}
		},

		/**
		 * Toggle options which dependency on autocomplete fields.
		 *
		 * @since 2.0.0
		 */
		toggleAutocompleteOptions: function() {

			if ( $( this ).prop( 'disabled' ) ) {
				return;
			}

			var $this = $( this ),
				enable = $this.prop( 'checked' ),
				$displayMapRow = $this.closest( '.wpforms-field-option-group-inner' ).find( '.wpforms-field-option-row-display_map' ),
				enableField = function() {

					$this.prop( 'disabled', false );
				};

			$this.prop( 'disabled', true );

			if ( enable ) {
				$displayMapRow.slideDown( '', enableField );

				return;
			}

			$displayMapRow.find( 'input' ).removeAttr( 'checked' ).trigger( 'change' );
			$displayMapRow.slideUp( '', enableField );
		},

		/**
		 * Toggle map.
		 *
		 * @since 2.0.0
		 */
		toggleMapDisplay: function() {

			if ( $( this ).prop( 'disabled' ) ) {
				return;
			}

			var $this = $( this ),
				enable = $this.prop( 'checked' ),
				$mapPositionRow = $this.closest( '.wpforms-field-option-group-inner' ).find( '.wpforms-field-option-row-map_position' ),
				mapPosition = $mapPositionRow.find( 'select' ).val(),
				fieldId = $this.closest( '.wpforms-field-option' ).data( 'field-id' ).toString(),
				enableField = function() {

					$this.prop( 'disabled', false );
				};

			$this.prop( 'disabled', true );

			if ( enable ) {
				app.updateMapPosition( fieldId, mapPosition );
				$mapPositionRow.slideDown( '', enableField );

				return;
			}

			app.hideMap( fieldId );
			$mapPositionRow.slideUp( '', enableField );
		},

		/**
		 * Toggle the Limit Length option.
		 *
		 * @since 2.0.0
		 */
		toggleLimitLength: function() {

			var enable = $( this ).prop( 'checked' ),
				parent = $( this ).closest( '.wpforms-field-option' ),
				id = parent.data( 'field-id' ),
				limitEnableField = $( '#wpforms-field-option-' + id + '-limit_enabled' );

			if ( ! limitEnableField.length ) {
				return;
			}

			if ( enable && limitEnableField.prop( 'checked' ) ) {
				limitEnableField.prop( 'checked', false ).trigger( 'change' );
				$.alert( {
					title: wpforms_builder.heads_up,
					content: wpforms_builder.disable_limit_length,
					icon: 'fa fa-exclamation-circle',
					type: 'orange',
					buttons: {
						confirm: {
							text: wpforms_builder.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
						},
					},
				} );
			}

			limitEnableField.prop( 'disabled', enable );
		},

		/**
		 * Change map position in preview field.
		 *
		 * @since 2.0.0
		 */
		changeMapPosition: function() {

			var fieldId = $( this ).closest( '.wpforms-field-option' ).data( 'field-id' ).toString();

			app.updateMapPosition( fieldId, $( this ).val() );
		},

		/**
		 * Hide map in the field preview.
		 *
		 * @since 2.0.0
		 *
		 * @param {number} fieldId Field ID.
		 */
		hideMap: function( fieldId ) {

			$( '#wpforms-field-' + fieldId + ' .wpforms-geolocation-map' ).hide();
		},

		/**
		 * Update map position in the field preview.
		 *
		 * @since 2.0.0
		 *
		 * @param {number} fieldId Field ID.
		 * @param {string} mapPosition Map position (above|below).
		 */
		updateMapPosition: function( fieldId, mapPosition ) {

			app.hideMap( fieldId );
			if ( 'above' === mapPosition ) {
				app.getAboveMapForPreviewField( fieldId ).show();
			} else if ( 'below' === mapPosition ) {
				app.getBelowMapForPreviewField( fieldId ).show();
			}
		},

		/**
		 * Get the above map for preview fields and create it if need.
		 *
		 * @since 2.0.0
		 *
		 * @param {number} fieldId Field ID.
		 *
		 * @returns {object} Map Above.
		 */
		getAboveMapForPreviewField: function( fieldId ) {

			var $fieldWrapper = $( '#wpforms-field-' + fieldId ),
				$firstField = $fieldWrapper.find( '.primary-input' ).length ? $fieldWrapper.find( '.primary-input' ) : $fieldWrapper.find( '.wpforms-address-scheme' ).first(),
				$mapAbove = $firstField.prev( '.wpforms-geolocation-map' );

			if ( ! $mapAbove.length ) {
				$mapAbove = $firstField.before( wpforms_builder.map.toString() );
				$mapAbove = $firstField.prev( '.wpforms-geolocation-map' );
			}

			return $mapAbove;
		},

		/**
		 * Get the below map for preview fields and create it if need.
		 *
		 * @since 2.0.0
		 *
		 * @param {number} fieldId Field ID.
		 *
		 * @returns {object} Map Above.
		 */
		getBelowMapForPreviewField: function( fieldId ) {

			var $fieldWrapper = $( '#wpforms-field-' + fieldId ),
				$fieldLast = $fieldWrapper.find( '.primary-input' ).length ? $fieldWrapper.find( '.primary-input' ) : $fieldWrapper.find( '.wpforms-address-scheme' ).last(),
				$mapBelow = $fieldLast.next( '.wpforms-geolocation-map' );

			if ( ! $mapBelow.length ) {
				$mapBelow = $fieldLast.after( wpforms_builder.map.toString() );
				$mapBelow = $fieldLast.next( '.wpforms-geolocation-map' );
			}

			return $mapBelow;
		},

		/**
		 * Set Place API Provider.
		 *
		 * @since 2.0.0
		 */
		fillPlacesAPI: function() {

			$.alert( {
				title: wpforms_builder.heads_up,
				content: wpforms_builder.places_provider_required,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsBuilderGeolocation.init();
