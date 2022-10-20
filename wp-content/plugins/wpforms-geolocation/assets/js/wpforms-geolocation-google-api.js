/* global wpforms_geolocation_settings, google */

'use strict';

/**
 * WPForms Geolocation Google Places API.
 *
 * @since 2.0.0
 */
var WPFormsGeolocationGooglePlacesAPI = window.WPFormsGeolocationGooglePlacesAPI || ( function( document, window ) {

	/**
	 * List of fields with autocomplete feature.
	 *
	 * @type {Array}
	 */
	var fieldsPlaces = [],

		/**
		 * List of the states from asset/json/states.json file.
		 *
		 * @type {object}
		 */
		states = {},

		/**
		 * Geodecoder from Geolocation API which help to detect current place by latitude and longitude.
		 *
		 * @type {object}
		 */
		geocoder;

	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 2.0.0
		 */
		init: function() {

			app.getFields();
			if ( ! fieldsPlaces.length ) {
				return;
			}
			app.initGeocoder();
			fieldsPlaces.forEach( function( currentFieldPlace ) {

				app.initMap( currentFieldPlace );
				app.initAutocomplete( currentFieldPlace );
			} );
			app.detectGeolocation();
		},

		/**
		 * Show debug message.
		 *
		 * @since 2.0.0
		 *
		 * @param {string|object} message Debug message.
		 */
		showDebugMessage: function( message ) {

			if ( ! window.location.hash || '#wpformsdebug' !== window.location.hash ) {
				return;
			}

			console.log( message );
		},

		/**
		 * Closest function.
		 *
		 * @param {Element} el Element.
		 * @param {string} selector Parent selector.
		 *
		 * @returns {Element|undefined} Parent.
		 */
		closest: function( el, selector ) {

			var matchesSelector = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;

			while ( el ) {
				if ( matchesSelector.call( el, selector ) ) {
					break;
				}
				el = el.parentElement;
			}
			return el;
		},

		/**
		 * Get all fields for geolocation.
		 *
		 * @since 2.0.0
		 */
		getFields: function() {

			var fields = Array.prototype.slice.call( document.querySelectorAll( '.wpforms-form .wpforms-field input[type="text"][data-autocomplete="1"]' ) );

			fields.forEach( function( el ) {

				var wrapper = app.closest( el, '.wpforms-field' ),
					mapField = el.hasAttribute( 'data-display-map' ) ? wrapper.querySelector( '.wpforms-geolocation-map' ) : null,
					type = wrapper.classList[ 1 ] ? wrapper.classList[ 1 ].replace( 'wpforms-field-', '' ) : 'text',
					additionalFields = {};

				if ( 'address' === type ) {
					var country = wrapper.querySelector( '.wpforms-field-address-country' );
					additionalFields = {
						locality: {
							el: wrapper.querySelector( '.wpforms-field-address-city' ),
							type: 'long_name',
						},
						political: {
							el: wrapper.querySelector( '.wpforms-field-address-state' ),
							type: country ? 'long_name' : 'short_name',
						},
						administrative_area_level_1: { // eslint-disable-line camelcase
							el: wrapper.querySelector( '.wpforms-field-address-state' ),
							type: country ? 'long_name' : 'short_name',
						},
						postal_code: { // eslint-disable-line camelcase
							el: wrapper.querySelector( '.wpforms-field-address-postal' ),
							type: 'long_name',
						},
						country: {
							el: country,
							type: 'short_name',
						},
					};
				}
				fieldsPlaces.push( {
					'searchField': el,
					'mapField': mapField,
					'type': type,
					'additionalFields': additionalFields,
				} );
			} );
		},

		/**
		 * Init Google Map.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 */
		initMap: function( currentFieldPlace ) {

			if ( ! currentFieldPlace.mapField ) {
				return;
			}
			var defaultLocation = wpforms_geolocation_settings.default_location,
				zoom = wpforms_geolocation_settings.zoom;

			currentFieldPlace.map = new google.maps.Map(
				currentFieldPlace.mapField,
				{
					zoom: zoom,
					center: defaultLocation,
				} );

			currentFieldPlace.marker = new google.maps.Marker(
				{
					position: defaultLocation,
					draggable: true,
					map: currentFieldPlace.map,
				} );

			currentFieldPlace.marker.addListener( 'dragend', app.markerDragend );
		},

		/**
		 * Init Google Geocoder.
		 *
		 * @since 2.0.0
		 */
		initGeocoder: function() {

			geocoder = new google.maps.Geocoder;
		},

		/**
		 * Action after marker was dragend.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} marker Google Marker.
		 */
		markerDragend: function( marker ) {

			var currentFieldPlace = app.findFieldPlaceByMarker( this );

			if ( ! currentFieldPlace ) {
				return;
			}

			app.detectByCoordinates(
				currentFieldPlace,
				marker.latLng );

			currentFieldPlace.map.setCenter( marker.latLng );
		},

		/**
		 * Detect Place by latitude and longitude.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 * @param {object} latLng Latitude and longitude.
		 */
		detectByCoordinates: function( currentFieldPlace, latLng ) {

			if ( ! geocoder ) {
				return;
			}

			geocoder.geocode( { 'location': latLng }, function( results, status ) {

				if ( status !== 'OK' ) {
					app.showDebugMessage( 'Geocode was wrong' );
					app.showDebugMessage( results );
					return;
				}
				if ( ! results[ 0 ] ) {
					return;
				}
				app.updateFields( currentFieldPlace, results[ 0 ] );
			} );
		},

		/**
		 * Update map.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 * @param {object} latLng Latitude and longitude.
		 */
		updateMap: function( currentFieldPlace, latLng ) {

			if ( ! currentFieldPlace.map ) {
				return;
			}

			currentFieldPlace.marker.setPosition( latLng );
			currentFieldPlace.map.setCenter( latLng );
		},

		/**
		 * Find current group field with places API by Google marker.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} marker Google marker.
		 *
		 * @returns {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByMarker: function( marker ) {

			var currentFieldPlace = null;

			fieldsPlaces.forEach( function( el ) {

				if ( el.marker !== marker ) {
					return;
				}
				currentFieldPlace = el;
			} );

			return currentFieldPlace;
		},

		/**
		 * Find current group field with places API by Google Autocomplete.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} autocomplete Google Autocomplete.
		 *
		 * @returns {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByAutocomplete: function( autocomplete ) {

			var currentFieldPlace = null;

			fieldsPlaces.forEach( function( el ) {

				if ( el.autocomplete !== autocomplete ) {
					return;
				}
				currentFieldPlace = el;
			} );

			return currentFieldPlace;
		},

		/**
		 * Find current group field with places API by country field element.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} countryEl Country field element.
		 *
		 * @returns {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByCountry: function( countryEl ) {

			var currentFieldPlace = null;

			fieldsPlaces.forEach( function( el ) {

				if ( ! el.additionalFields || ! el.additionalFields.country || el.additionalFields.country.el !== countryEl ) {
					return;
				}

				currentFieldPlace = el;
			} );

			return currentFieldPlace;
		},

		/**
		 * Find current group field with places API by state field element.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} politicalEl State field element.
		 *
		 * @returns {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByPolitical: function( politicalEl ) {

			var currentFieldPlace = null;

			fieldsPlaces.forEach( function( el ) {

				if ( ! el.additionalFields || ! el.additionalFields.political || el.additionalFields.political.el !== politicalEl ) {
					return;
				}

				currentFieldPlace = el;
			} );

			return currentFieldPlace;
		},

		/**
		 * Init Google Autocomplete.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 */
		initAutocomplete: function( currentFieldPlace ) {

			currentFieldPlace.autocomplete = new google.maps.places.Autocomplete(
				currentFieldPlace.searchField,
				{
					types: [ 'geocode' ],
				} );

			currentFieldPlace.autocomplete.addListener( 'place_changed', app.updateFieldPlace );

			if ( 'address' === currentFieldPlace.type ) {
				app.initAutocompleteAddress( currentFieldPlace );
			}
		},

		/**
		 * Init address field autocomplete features.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 */
		initAutocompleteAddress: function( currentFieldPlace ) {

			app.disableBrowserAutocomplete( currentFieldPlace.searchField );

			if ( currentFieldPlace.additionalFields.country.el ) {
				currentFieldPlace.additionalFields.country.el.addEventListener( 'change', app.updateCountry );
				return;
			}

			// Only for US Address field.
			currentFieldPlace.autocomplete.setComponentRestrictions( {
				'country': [ 'us' ],
			} );

			if ( currentFieldPlace.additionalFields.political.el ) {
				const xhr = new XMLHttpRequest;

				xhr.onreadystatechange = function() {

					if ( xhr.readyState === 4 && xhr.status === 200 ) {
						states = JSON.parse( xhr.responseText );

						for ( var code in states ) {
							if ( ! Object.prototype.hasOwnProperty.call( states, code ) ) {
								continue;
							}

							delete states[ code ].name;
						}
					}
				};
				xhr.open( 'GET', wpforms_geolocation_settings.states );
				xhr.send();

				currentFieldPlace.additionalFields.political.el.addEventListener( 'change', app.updateArea );
			}
		},

		/**
		 * Disable Chrome browser autocomplete.
		 *
		 * @since 2.0.0
		 *
		 * @param {Element} searchField Search field.
		 */
		disableBrowserAutocomplete: function( searchField ) {

			if ( navigator.userAgent.indexOf( 'Chrome' ) === -1 ) {
				return;
			}

			var observerHack = new MutationObserver( function() {
				observerHack.disconnect();
				searchField.setAttribute( 'autocomplete', 'chrome-off' );
			} );

			observerHack.observe( searchField, {
				attributes: true,
				attributeFilter: [ 'autocomplete' ],
			} );
		},

		/**
		 * Update field place when Google Autocomplete field fill.
		 *
		 * @since 2.0.0
		 */
		updateFieldPlace: function() {

			var currentFieldPlace = app.findFieldPlaceByAutocomplete( this );

			if ( ! currentFieldPlace || ! currentFieldPlace.autocomplete ) {
				return;
			}

			var place = currentFieldPlace.autocomplete.getPlace();

			if ( ! place.geometry.location ) {
				return;
			}

			app.updateMap( currentFieldPlace, place.geometry.location );
			app.updateFields( currentFieldPlace, place );
		},

		/**
		 * Update fields at specified place.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 * @param {object} place Current place.
		 */
		updateFields: function( currentFieldPlace, place ) {

			if ( ! Object.prototype.hasOwnProperty.call( place, 'formatted_address' ) ) {
				return;
			}

			if ( 'text' === currentFieldPlace.type ) {
				app.updateTextField( currentFieldPlace, place );
			} else if ( 'address' === currentFieldPlace.type ) {
				app.updateAddressField( currentFieldPlace, place );
			}

			app.triggerEvent( currentFieldPlace.searchField, 'change' );

			app.showDebugMessage( 'Fields was updated' );
			app.showDebugMessage( currentFieldPlace );
			app.showDebugMessage( place );
		},

		/**
		 * Update text field at specified place.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 * @param {object} place Current place.
		 */
		updateTextField: function( currentFieldPlace, place ) {

			currentFieldPlace.searchField.value = place.formatted_address;
		},

		/**
		 * Trigger JS event.
		 *
		 * @since 2.0.0
		 *
		 * @param {Element} el Element.
		 * @param {string} eventName Event name.
		 */
		triggerEvent: function( el, eventName ) {

			var e = document.createEvent( 'HTMLEvents' );

			e.initEvent( eventName, true, true );
			el.dispatchEvent( e );
		},

		/**
		 * Update address fields at specified place.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 * @param {object} place Current place.
		 */
		updateAddressField: function( currentFieldPlace, place ) {

			var street = '',
				streetNumber = '';

			for ( var i = 0; i < place.address_components.length; i++ ) {
				var addressType = place.address_components[ i ].types[ 0 ];
				if ( 'route' === addressType ) {
					street = place.address_components[ i ][ 'short_name' ];

					continue;
				}

				if ( 'street_number' === addressType ) {
					streetNumber = place.address_components[ i ][ 'short_name' ];

					continue;
				}

				if ( currentFieldPlace.additionalFields[ addressType ] && currentFieldPlace.additionalFields[ addressType ].el ) {
					currentFieldPlace.additionalFields[ addressType ].el.value = place.address_components[ i ][ currentFieldPlace.additionalFields[ addressType ].type ];
				}
			}

			currentFieldPlace.searchField.value = app.formatAddressField( place, streetNumber, street );
		},

		/**
		 * Get formatted address.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} place Current place.
		 * @param {string} streetNumber Street number.
		 * @param {string} street Street name.
		 *
		 * @returns {string} Formatted address.
		 */
		formatAddressField: function( place, streetNumber, street ) {

			var address = 0 === place.formatted_address.indexOf( streetNumber ) ?
				streetNumber + ' ' + street : // US format.
				street + ', ' + streetNumber; // EU format.

			// Remove spaces and commas at the start or end of the string.
			return address.trim().replace( /,$|^,/g, '' );
		},

		/**
		 * Update country for address field. Conditional strict. Start work after CUSTOMER change a country field.
		 *
		 * @since 2.0.0
		 */
		updateCountry: function() {

			var currentFieldPlace = app.findFieldPlaceByCountry( this ),
				countryCode = this.value.toString().toLocaleLowerCase();

			if ( ! currentFieldPlace || ! currentFieldPlace.autocomplete ) {
				return;
			}

			currentFieldPlace.autocomplete.setComponentRestrictions( {
				'country': [ countryCode ],
			} );

			app.showDebugMessage( 'Autocomplete field restrict to country: ' + countryCode );
		},

		/**
		 * Update state for address field. Conditional not strict. Start work after CUSTOMER change a state field.
		 *
		 * @since 2.0.0
		 */
		updateArea: function() {

			var currentFieldPlace = app.findFieldPlaceByPolitical( this ),
				stateCode = this.value.toString().toUpperCase();

			if ( ! currentFieldPlace || ! currentFieldPlace.autocomplete ) {
				return;
			}

			if ( ! states[ stateCode ] ) {
				return;
			}

			currentFieldPlace.autocomplete.setBounds( new google.maps.LatLngBounds( states[ stateCode ] ) );

			app.showDebugMessage( 'Autocomplete field restrict to state: ' + stateCode );
		},

		/**
		 * Detect customer geolocation.
		 *
		 * @since 2.0.0
		 */
		detectGeolocation: function() {

			if ( ! wpforms_geolocation_settings.current_location  || ! navigator.geolocation || ! fieldsPlaces ) {
				return;
			}

			var geolocation = {};

			navigator.geolocation.getCurrentPosition( function( position ) {

				geolocation = {
					lat: position.coords.latitude,
					lng: position.coords.longitude,
				};
				fieldsPlaces.forEach( function( currentFieldPlace ) {

					app.updateMap( currentFieldPlace, geolocation );
					app.detectByCoordinates( currentFieldPlace, geolocation );
				} );
			} );
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window ) );

// Load script if google geolocation library was included from another theme or plugin.
window.addEventListener( 'load', WPFormsGeolocationGooglePlacesAPI.init );

/**
 * Use function callback for running throw Google JS API.
 *
 * @since 2.0.0
 */
function WPFormsGeolocationInitGooglePlacesAPI() { // eslint-disable-line no-unused-vars

	window.removeEventListener( 'load', WPFormsGeolocationGooglePlacesAPI.init );
	WPFormsGeolocationGooglePlacesAPI.init();
}
