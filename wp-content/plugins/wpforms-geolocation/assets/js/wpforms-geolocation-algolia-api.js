/* global wpforms_geolocation_settings, places, L, algoliasearch */

'use strict';

/**
 * WPForms Geolocation Algolia Places API.
 *
 * @since 2.0.0
 */
var WPFormsGeolocationAlgoliaPlacesAPI = window.WPFormsGeolocationAlgoliaPlacesAPI || ( function( document, window ) {

	/**
	 * List of fields with autocomplete feature.
	 *
	 * @type {Array}
	 */
	var fieldsPlaces = [],

		/**
		 * List of the states from the asset/json/states.json file.
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

			if ( document.readyState === 'loading' ) {
				document.addEventListener( 'DOMContentLoaded', app.ready );
			} else {
				app.ready();
			}
		},

		/**
		 * Document ready.
		 *
		 * @since 2.0.0
		 */
		ready: function() {

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
					additionalFields = {
						city: wrapper.querySelector( '.wpforms-field-address-city' ),
						suburb: wrapper.querySelector( '.wpforms-field-address-state' ),
						postcode: wrapper.querySelector( '.wpforms-field-address-postal' ),
						countryCode: wrapper.querySelector( '.wpforms-field-address-country' ),
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
		 * Init Algolia Map.
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

			currentFieldPlace.map = L.map(
				currentFieldPlace.mapField,
				{
					zoom: zoom,
					center: defaultLocation,
				} );

			var osmLayer = new L.TileLayer(
				'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
				{
					minZoom: 5,
					maxZoom: 18,
				} );

			currentFieldPlace.map.addLayer( osmLayer );
			app.updateMap( currentFieldPlace, defaultLocation );

			if ( ! currentFieldPlace.mapField.parentElement.classList.contains( 'wpforms-conditional-field' ) ) {
				return;
			}

			// Resize map when the field after change visibility.
			var observer = new MutationObserver( function( mutations ) {
				mutations.forEach( function( mutationRecord ) {
					currentFieldPlace.map.invalidateSize();
				} );
			} );
			observer.observe( currentFieldPlace.mapField.parentElement, { attributes: true, attributeFilter: [ 'style' ] } );
		},

		/**
		 * Init Algolia Geocoder.
		 *
		 * @since 2.0.0
		 */
		initGeocoder: function() {

			geocoder = algoliasearch.initPlaces( wpforms_geolocation_settings.app_id, wpforms_geolocation_settings.api_key );
		},

		/**
		 * Action after marker was dragend.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} marker Algolia Marker.
		 */
		markerDragend: function( marker ) {

			var currentFieldPlace = app.findFieldPlaceByMarker( this );

			if ( ! currentFieldPlace ) {
				return;
			}

			app.detectByCoordinates(
				currentFieldPlace,
				{
					lat: marker.latLng.lat(),
					lng: marker.latLng.lng(),
				} );
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

			if ( ! currentFieldPlace.autocomplete ) {
				return;
			}

			geocoder.reverse( {
				aroundLatLng: latLng.lat + ',' + latLng.lng,
				hitsPerPage: 1,
			} ).then( function( response ) {

				if ( ! response.hits || ! response.hits[ 0 ] ) {
					return;
				}
				app.updateFields( currentFieldPlace, response.hits[ 0 ] );
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

			if ( currentFieldPlace.marker ) {
				currentFieldPlace.map.removeLayer( currentFieldPlace.marker );
			}

			currentFieldPlace.marker = L.marker(
				latLng,
				{
					opacity: 1,
					draggable: true,
				} );

			currentFieldPlace.marker.addTo( currentFieldPlace.map );
			currentFieldPlace.map.setView( latLng );
			currentFieldPlace.marker.on( 'moveend', app.markerChanged );
		},

		/**
		 * Marker changed event.
		 *
		 * @since 2.0.0
		 */
		markerChanged: function() {

			var currentFieldPlace = app.findFieldPlaceByMap( this._map );
			app.updateMap( currentFieldPlace, this._latlng );
			app.detectByCoordinates( currentFieldPlace, this._latlng );
		},

		/**
		 * Detect Current Place by Map.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} map Map.
		 *
		 * @returns {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByMap: function( map ) {

			var currentFieldPlace = null;

			fieldsPlaces.forEach( function( el ) {

				if ( el.map !== map ) {
					return;
				}
				currentFieldPlace = el;
			} );

			return currentFieldPlace;
		},

		/**
		 * Find current group field with places API by Algolia Autocomplete.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} autocomplete Algolia Autocomplete.
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
		 * @param {object} countryCode Country field element.
		 *
		 * @returns {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByCountryCode: function( countryCode ) {

			var currentFieldPlace = null;

			fieldsPlaces.forEach( function( el ) {

				if ( ! el.additionalFields || ! el.additionalFields.countryCode || el.additionalFields.countryCode !== countryCode ) {
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
		 * @param {object} stateEl State field element.
		 *
		 * @returns {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByState: function( stateEl ) {

			var currentFieldPlace = null;

			fieldsPlaces.forEach( function( el ) {

				if ( ! el.additionalFields ) {
					return;
				}
				if ( ! el.additionalFields.suburb ) {
					return;
				}
				if ( el.additionalFields.suburb !== stateEl ) {
					return;
				}
				currentFieldPlace = el;
			} );

			return currentFieldPlace;
		},

		/**
		 * Init Algolia Autocomplete.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 */
		initAutocomplete: function( currentFieldPlace ) {

			var placesSettings = {
					appId: wpforms_geolocation_settings.app_id,
					apiKey: wpforms_geolocation_settings.api_key,
					container: currentFieldPlace.searchField,
				},
				configure = {
					language: wpforms_geolocation_settings.language,
					type: 'address',
					style: false,
				};

			if ( 'address' === currentFieldPlace.type ) {
				app.initAutocompleteAddressField( currentFieldPlace, placesSettings, configure );
			} else {
				app.initAutocompleteTextField( currentFieldPlace, placesSettings, configure );
			}
		},

		/**
		 * Init Algolia for the text field.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 * @param {object} placesSettings Default settings for the search API.
		 * @param {object} configure Additional settings for the search API.
		 */
		initAutocompleteTextField: function( currentFieldPlace, placesSettings, configure ) {

			var parent = currentFieldPlace.searchField.parentNode,
				classList = Array.prototype.slice.call( currentFieldPlace.searchField.classList );

			classList.forEach( function( className ) {

				if ( className.indexOf( 'wpforms-field-' ) === -1 ) {
					return;
				}
				currentFieldPlace.searchField.classList.remove( className );
				parent.classList.add( className );
			} );

			parent.classList.add( 'wpforms-field-row' );

			if ( currentFieldPlace.mapField ) {
				currentFieldPlace.mapField.classList.remove( 'wpforms-field-small' );
				currentFieldPlace.mapField.classList.remove( 'wpforms-field-medium' );
				currentFieldPlace.mapField.classList.remove( 'wpforms-field-large' );
			}

			currentFieldPlace.autocomplete = places( placesSettings ).configure( configure );

			app.bindTextFieldEvents( currentFieldPlace );
		},

		/**
		 * Bind events for the text field.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 */
		bindTextFieldEvents: function( currentFieldPlace ) {

			currentFieldPlace.autocomplete
				.on( 'change', app.updateFieldPlace )
				.on( 'clear', app.clearButtonCallback );

			currentFieldPlace.searchField.addEventListener( 'focusin', app.addActiveClass );
			currentFieldPlace.searchField.addEventListener( 'focusout', app.removeActiveClass );
		},

		/**
		 * Add a class for the container when the the field is active.
		 *
		 * @since 2.0.0
		 */
		addActiveClass: function() {

			this.parentElement.classList.add( 'algolia-places-active' );
		},

		/**
		 * Remove a class for the container when the the field is non-active.
		 *
		 * @since 2.0.0
		 */
		removeActiveClass: function() {

			this.parentElement.classList.remove( 'algolia-places-active' );
		},

		/**
		 * Click on clear button.
		 *
		 * @since 2.0.0
		 */
		clearButtonCallback: function() {

			var currentFieldPlace = app.findFieldPlaceByAutocomplete( this );

			if ( ! currentFieldPlace ) {
				return;
			}

			app.triggerEvent( currentFieldPlace.searchField, 'change' );
		},

		/**
		 * Init Algolia for the address field.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 * @param {object} placesSettings Default settings for the search API.
		 * @param {object} configure Additional settings for the search API.
		 */
		initAutocompleteAddressField: function( currentFieldPlace, placesSettings, configure ) {

			placesSettings.templates = {
				value: function( suggestion ) {
					return suggestion.name;
				},
			};

			if ( ! currentFieldPlace.additionalFields.countryCode ) {
				configure.countries = [ 'us' ];
			}

			currentFieldPlace.autocomplete = places( placesSettings ).configure( configure );

			// Disable autocomplete at the Chrome browser. For other browsers, this is disabled by default.
			if ( navigator.userAgent.indexOf( 'Chrome' ) !== -1 ) {
				currentFieldPlace.searchField.setAttribute( 'autocomplete', 'chrome-off' );
			}

			app.bindAddressFieldEvents( currentFieldPlace );
		},

		/**
		 * Bind events for the address field..
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 */
		bindAddressFieldEvents: function( currentFieldPlace ) {

			if ( currentFieldPlace.additionalFields.countryCode ) {
				currentFieldPlace.additionalFields.countryCode.addEventListener( 'change', app.updateCountry );
			}

			// Only for US Address field.
			if ( ! currentFieldPlace.additionalFields.countryCode && currentFieldPlace.additionalFields.suburb ) {
				const xhr = new XMLHttpRequest;

				xhr.onreadystatechange = function() {

					if ( xhr.readyState === 4 && xhr.status === 200 ) {
						var s = JSON.parse( xhr.responseText );

						for ( var code in s ) {
							if ( ! Object.prototype.hasOwnProperty.call( s, code ) ) {
								continue;
							}

							var state = s[ code ],
								name = state.name;

							delete state.name;
							states[ name ] = state;
							states[ name ].code = code;
						}
					}
				};
				xhr.open( 'GET', wpforms_geolocation_settings.states );
				xhr.send();

				currentFieldPlace.additionalFields.suburb.addEventListener( 'change', app.updateArea );
			}

			currentFieldPlace.autocomplete
				.on( 'change', app.updateFieldPlace )
				.on( 'clear', app.clearButtonCallback );

			currentFieldPlace.searchField.addEventListener( 'focusin', app.addActiveClass );
			currentFieldPlace.searchField.addEventListener( 'focusout', app.removeActiveClass );
		},

		/**
		 * Update fields on update autocomplete field.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} e Element.
		 */
		updateFieldPlace: function( e ) {

			if ( ! e.suggestion || ! e.suggestion.hit || ! e.suggestion.latlng ) {
				return;
			}

			var currentFieldPlace = app.findFieldPlaceByAutocomplete( this );

			if ( ! currentFieldPlace ) {
				return;
			}

			app.updateFields( currentFieldPlace, e.suggestion.hit );
			app.updateMap( currentFieldPlace, e.suggestion.latlng );
		},

		/**
		 * Update fields using some place.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 * @param {object} place Current place.
		 */
		updateFields: function( currentFieldPlace, place ) {

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
		 * Update text field using some place.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} currentFieldPlace Current group field with places API.
		 * @param {object} place Current place.
		 */
		updateTextField: function( currentFieldPlace, place ) {

			var address = [],
				addressFields = [
					'locale_names',
					'city',
					'administrative',
					'country',
				];

			addressFields.forEach( function( item ) {

				if ( place[ item ] ) {
					address.push( app.getTranslatedPlaceField( item, place ) );
				}
			} );
			address.filter( function( item ) {

				return item.toString().trim();
			} );

			currentFieldPlace.autocomplete.setVal( address.join( ', ' ) );
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
		 * Get translated place field.
		 *
		 * @since 2.0.0
		 *
		 * @param {string} key Place key.
		 * @param {object} place Current place.
		 *
		 * @returns {string} Translated place property.
		 */
		getTranslatedPlaceField: function( key, place ) {

			if ( ! place[ key ] ) {
				return '';
			}
			if ( 'string' === typeof place[ key ] ) {
				return place[ key ];
			}

			if ( place[ key ].default ) {
				return place[ key ][ wpforms_geolocation_settings.language ] ? place[ key ][ wpforms_geolocation_settings.language ] : place[ key ].default;
			}

			return place[ key ][ 0 ] ? place[ key ][ 0 ] : '';
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

			var fields = [
				'city',
				'postcode',
			];
			fields.forEach( function( fieldName ) {

				if ( place[ fieldName ] && currentFieldPlace.additionalFields[ fieldName ] ) {
					currentFieldPlace.additionalFields[ fieldName ].value = app.getTranslatedPlaceField( fieldName, place );
				}
			} );

			app.updateAddressFieldState( currentFieldPlace.additionalFields.suburb, place );

			if ( place.country_code && currentFieldPlace.additionalFields.countryCode ) {
				currentFieldPlace.additionalFields.countryCode.value = place.country_code.toString().toUpperCase();
			}

			if ( place.locale_names && place.locale_names.default ) {
				currentFieldPlace.autocomplete.setVal( app.getTranslatedPlaceField( 'locale_names', place ) );
			}
		},

		/**
		 * Update state field in address field group.
		 *
		 * @since 2.0.0
		 *
		 * @param {object} stateField State field element.
		 * @param {object} place Place object.
		 */
		updateAddressFieldState: function( stateField, place ) {

			var value;

			if ( place.administrative ) {
				value = app.getTranslatedPlaceField( 'administrative', place );
			}

			if ( ! value && place.suburb ) {
				value = app.getTranslatedPlaceField( 'suburb', place );
			}

			if ( value && 'select' === stateField.tagName.toLowerCase() ) {
				value = app.getStateCodyByName( value );
			}

			stateField.value = value;
		},

		/**
		 * Get state code by name.
		 *
		 * @param {string} stateName State name
		 *
		 * @returns {string} State code.
		 */
		getStateCodyByName: function( stateName ) {

			return states[ stateName ] && Object.prototype.hasOwnProperty.call( states[ stateName ], 'code' ) ? states[ stateName ].code : '';
		},

		/**
		 * Update country for address field. Conditional not strict. Start work after CUSTOMER change a country field.
		 *
		 * @since 2.0.0
		 */
		updateCountry: function() {

			var currentFieldPlace = app.findFieldPlaceByCountryCode( this ),
				countryCode = this.value.toString().toLocaleLowerCase();

			if ( ! currentFieldPlace || ! currentFieldPlace.autocomplete ) {
				return;
			}

			currentFieldPlace.autocomplete.configure( {
				countries: [ countryCode ],
				language: wpforms_geolocation_settings.language,
				type: 'address',
			} );

			app.showDebugMessage( 'Autocomplete field restrict to country: ' + countryCode );
		},

		/**
		 * Update state for address field. Conditional not strict. Start work after CUSTOMER change a state field.
		 *
		 * @since 2.0.0
		 */
		updateArea: function() {

			var currentFieldPlace = app.findFieldPlaceByState( this ),
				stateCode = this.value.toString().toUpperCase();

			if ( ! currentFieldPlace || ! currentFieldPlace.autocomplete ) {
				return;
			}

			if ( ! states[ stateCode ] ) {
				return;
			}

			currentFieldPlace.autocomplete.configure( {
				countries: [ 'us' ],
				language: wpforms_geolocation_settings.language,
				type: 'address',
				aroundLatLng: Object.values( states[ stateCode ] ).join(),
			} );

			app.showDebugMessage( 'Autocomplete field restrict to state: ' + stateCode );
		},

		/**
		 * Detect customer geolocation.
		 *
		 * @since 2.0.0
		 */
		detectGeolocation: function() {

			if ( ! wpforms_geolocation_settings.current_location || ! navigator.geolocation || ! fieldsPlaces ) {
				return;
			}

			var geolocation = {};

			navigator.geolocation.getCurrentPosition( function( position ) {

				geolocation = {
					lat: position.coords.latitude.toFixed( 6 ),
					lng: position.coords.longitude.toFixed( 6 ),
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

// Initialize.
WPFormsGeolocationAlgoliaPlacesAPI.init();
