/* globals wpforms_conversational_forms, MobileDetect */

'use strict';

/**
 * WPForms Conversational Forms function.
 *
 * @since 1.0.0
 * @package WPFormsConversationalForms
 *
 * @namespace
 */
var WPFormsConversationalForms = window.WPFormsConversationalForms || ( function( document, window, $ ) {

	var elements,
		helpers,
		scrollControl,
		eventMapControl,
		mainClasses,
		childClasses,
		globalEvents,
		globalEventsMobile,
		app;

	/**
	 * Element aliases.
	 *
	 * @since 1.0.0
	 */
	elements = {

		page              : $( '#wpforms-conversational-form-page' ),
		form              : $( '.wpforms-form' ),
		header            : $( '.wpforms-conversational-form-header' ),
		phpErrorContainer : $( '.wpforms-error-container' ),
		fields            : $( '.wpforms-field-container .wpforms-field' ),
		recaptchaContainer: $( '.wpforms-recaptcha-container' ),
		footer            : $( '.wpforms-submit-container' ),
		progress          : {
			bar       : $( '.wpforms-conversational-form-footer-progress-completed' ),
			completed : $( '.wpforms-conversational-form-footer-progress-status .completed' ),
			totalCount: $( '.wpforms-conversational-form-footer-progress-status .completed-of' ),
		},
	};

	/**
	 * Helper methods.
	 *
	 * @since 1.0.0
	 */
	helpers = {

		/**
		 * String helpers.
		 *
		 * @since 1.0.0
		 */
		string: {

			/**
			 * Capitalize and camelcase string.
			 *
			 * @since 1.0.0
			 *
			 * @param {string} str String to process.
			 *
			 * @returns {string} Capitalized camelcased string.
			 */
			toCapitalizedCamelCase: function( str ) {

				if ( ! str || typeof str !== 'string' ) {
					return str;
				}

				return str
					.replace( /[\s-_](.)/g, function( $1 ) {
						return $1.toUpperCase();
					} )
					.replace( /[\s-_]/g, '' )
					.replace( /^(.)/, function( $1 ) {
						return $1.toUpperCase();
					} );
			},
		},

		/**
		 * Object helpers.
		 *
		 * @since 1.0.0
		 */
		object: {

			/**
			 * Check if object is empty.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} obj Object to evaluate.
			 *
			 * @returns {boolean} Object is empty.
			 */
			isEmpty: function( obj ) {

				if ( typeof obj !== 'object' ) {
					return true;
				}

				return ! Object.keys( obj ).length;
			},

			/**
			 * Get contents of an object's next/previous key relative to a given one.
			 *
			 * Example: { a: 10, b: 8, c: 34, d: 9, e: 15 }
			 * To get contents of next next element to 'b' without knowing target's index
			 * 'key' param needs to be set to 'b', and 'relIndex' to 2.
			 * Contents of 'd' will be returned.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} obj Object to look into.
			 * @param {string} key Starting key for relative iteration.
			 * @param {number} relIndex - Number of next/prev iterations to perform.
			 * Negative numbers mean look backwards, positive - forward.
			 *
			 * @returns {*} Found key contents.
			 */
			findSequentialKey: function( obj, key, relIndex ) {

				relIndex = relIndex || 0;

				var value,
					keys = Object.keys( obj ),
					keyFound = keys[ ( keys.indexOf( key.toString() ) + relIndex ) ];

				if ( keyFound in obj ) {
					value = obj[ keyFound ];
				}

				return value;
			},

			/**
			 * Get contents of an object's next key relative to a given one.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} obj Object to look into.
			 * @param {string} key Starting key for relative iteration.
			 *
			 * @returns {*} Found key contents.
			 */
			findNextKey: function( obj, key ) {

				return helpers.object.findSequentialKey( obj, key, 1 );
			},

			/**
			 * Get contents of an object's previous key relative to a given one.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} obj Object to look into.
			 * @param {string} key Starting key for relative iteration.
			 *
			 * @returns {*} Found key contents.
			 */
			findPrevKey: function( obj, key ) {

				return helpers.object.findSequentialKey( obj, key, -1 );
			},

			/**
			 * Get object's first key value.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} obj Object to look into.
			 *
			 * @returns {*} Found key contents.
			 */
			findFirstKey: function( obj ) {

				return helpers.object.getKeyByNumIndex( obj, 0 );
			},

			/**
			 * Get object's last key value.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} obj Object to look into.
			 *
			 * @returns {*} Found key contents.
			 */
			findLastKey: function( obj ) {

				return helpers.object.getKeyByNumIndex( obj, Object.keys( obj ).length - 1 );
			},

			/**
			 * Get object's key numerical index by its name.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} obj Object to look into.
			 * @param {string} key Key name.
			 *
			 * @returns {number} Key numerical index.
			 */
			getNumKeyIndex: function( obj, key ) {

				return Object.keys( obj ).indexOf( key.toString() );
			},

			/**
			 * Get object's key value by its numerical index similar to array.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} obj Object to look into.
			 * @param {number} index Key numerical index (zero-based).
			 *
			 * @returns {*} Found key contents.
			 */
			getKeyByNumIndex: function( obj, index ) {

				index = index || 0;

				var value,
					keys = Object.keys( obj ),
					keyFound = keys[ index ];

				if ( keyFound in obj ) {
					value = obj[ keyFound ];
				}

				return value;
			},
		},

		/**
		 * Class helpers.
		 *
		 * @since 1.0.0
		 */
		class: {

			/**
			 * Extend subClass with prototype methods from superClass.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} subClass Methods recepient class.
			 * @param {object} superClass Methods donor class.
			 */
			extend: function( subClass, superClass ) {

				var subClassPrototype = subClass.prototype;

				subClass.prototype = Object.create( superClass.prototype );

				Object.keys( subClassPrototype ).forEach( function( propName ) {
					subClass.prototype[ propName ] = subClassPrototype[ propName ];
				} );

				subClass.prototype.constructor = subClass;
				subClass.superclass = superClass.prototype;
				if ( superClass.prototype.constructor === Object.prototype.constructor ) {
					superClass.prototype.constructor = superClass;
				}
			},
		},
	};

	/**
	 * Controls both app and user page scrolling.
	 *
	 * @since 1.0.0
	 */
	scrollControl = {

		/**
		 * Position top of an active field relative to viewport.
		 *
		 * @since 1.0.0
		 */
		baseline: ( function() {

			var divisor = 3;

			if ( 'ontouchstart' in window || window.navigator.maxTouchPoints > 0 ) {
				divisor = 5;
			}

			return window.innerHeight / divisor;
		}() ),

		/**
		 * Check if a page has zero scroll position.
		 *
		 * @since 1.0.0
		 *
		 * @returns {boolean} Page has zero scroll position.
		 */
		isTop: function() {

			return 0 === $( window ).scrollTop();
		},

		/**
		 * Scroll the top of the form header to a baseline (see scrollControl.baseline).
		 *
		 * @since 1.0.0
		 */
		top: function() {

			elements.page.addClass( 'wpforms-conversational-form-start' );
			app.scroll.to( elements.header );
		},

		/**
		 * Scroll the top of the first form field to a baseline (see scrollControl.baseline).
		 *
		 * @since 1.0.0
		 */
		start: function() {

			var item;

			elements.page.removeClass( 'wpforms-conversational-form-start' );

			item = helpers.object.findFirstKey( app.fields.registered );

			while ( item && ! item.$el.is( ':visible' ) ) {
				item = helpers.object.findNextKey( app.fields.registered, item.id );
			}

			if ( item ) {
				app.scroll.to( item );
			}
		},

		/**
		 * Scroll the top of the form footer to a baseline (see scrollControl.baseline).
		 *
		 * @since 1.0.0
		 */
		finish: function() {

			// Scroll without validation if there's nothing to validate.
			if ( ! app.fields.active ) {
				app.scroll.to( elements.footer );
				return;
			}

			// Scroll only if active element is valid.
			if ( app.fields.active.validate() ) {
				app.scroll.to( elements.footer );
			}
		},

		/**
		 * Scroll the top of the next field to a baseline after Conditional Logic processing is done.
		 *
		 * @since 1.0.0
		 * @since {VERSION} Added Conditional Logic compatibility.
		 */
		next: function() {

			if ( app.fields.active && app.fields.active.$el.hasClass( 'wpforms-conditional-trigger' ) ) {
				$( document ).one( 'wpformsProcessConditionals', app.scroll.processNext );
			} else {
				app.scroll.processNext();
			}
		},

		/**
		 * Scroll the top of the next field to a baseline (see scrollControl.baseline).
		 *
		 * @since {VERSION}
		 */
		processNext: function() {

			var nextField = app.fields.findNext();

			if ( ! nextField && elements.page.hasClass( 'wpforms-conversational-form-start' ) ) {
				app.scroll.start();
				return;
			}

			if ( ! nextField && app.fields.isAtBaseline( elements.header ) ) {
				app.scroll.start();
				return;
			}

			if ( ! nextField && app.fields.isAtBaseline( elements.footer ) ) {
				return;
			}

			if ( ! nextField ) {
				app.scroll.finish();
				return;
			}

			if ( ! app.fields.active.validate() ) {
				return;
			}

			app.scroll.to( nextField );
		},

		/**
		 * Scroll the top of the previous field to a baseline (see scrollControl.baseline).
		 *
		 * @since 1.0.0
		 */
		prev: function() {

			var prevField = app.fields.findPrev();

			if ( ! prevField && app.fields.isAtBaseline( elements.header ) ) {
				return;
			}

			if ( ! prevField && app.fields.isAtBaseline( elements.footer ) ) {

				prevField = helpers.object.findLastKey( app.fields.registered );

				while ( prevField && ! prevField.$el.is( ':visible' ) ) {
					prevField = helpers.object.findPrevKey( app.fields.registered, prevField.id );
				}
			}

			if ( ! prevField ) {
				return;
			}

			app.scroll.to( prevField );
		},

		/**
		 * Actions before app starts to scroll the page.
		 *
		 * @since 1.0.0
		 */
		before: function() {

			$( window ).off( 'scroll', app.scroll.passive );

			app.unmapAllGlobalEvents();
		},

		/**
		 * Actions after app finishes to scroll the page.
		 *
		 * @since 1.0.0
		 */
		after: function() {

			// TODO: Check order of invocation, make it same as in app.fields.updateActive.
			app.mapAllGlobalEvents();

			app.fields.callOnActive( 'activate' );

			$( window ).on( 'scroll', app.scroll.passive );

			app.updateProgressBar();
		},

		/**
		 * Animate scrolling of the given jQuery element's top to a baseline (see scrollControl.baseline).
		 *
		 * @since 1.1.0
		 *
		 * @param {jQuery} $el jQuery element.
		 *
		 * @returns {jQuery.Deferred} jQuery object for callbacks.
		 */
		animate: function( $el ) {

			if ( ! $el || ! $el.length ) {
				return $.Deferred().resolve(); // eslint-disable-line new-cap
			}

			var position = $el.offset().top - app.scroll.baseline + 1;

			app.scroll.before();

			return $( 'html, body' )
				.animate( {
					scrollTop: position,
				}, 350 )
				.promise()
				.then( app.scroll.after );
		},

		/**
		 * Scroll the top of the given field or jQuery element to a baseline (see scrollControl.baseline).
		 *
		 * @since 1.0.0
		 *
		 * @param {mainClasses.Field|jQuery} field Field object or jQuery element.
		 *
		 * @returns {jQuery.Deferred} jQuery object for callbacks.
		 */
		to: function( field ) {

			var $el = field.$el || field;

			if ( ! $el || ! $el.length ) {
				return;
			}

			app.fields.updateActive( field, true );

			if ( field instanceof mainClasses.Field && field.items.current ) {
				$el = field.items.current.$el;
			}

			return app.scroll.animate( $el );
		},

		/**
		 * User scrolls the page.
		 *
		 * @since 1.0.0
		 */
		passive: function() {

			var winScroll = $( window ).scrollTop();

			if ( ! app.fields.active ) {
				app.fields.updateActive();
			}

			if ( 0 === winScroll ) {
				return;
			}

			if ( winScroll > 0 && elements.page.hasClass( 'wpforms-conversational-form-start' ) ) {
				app.scroll.start();
				return;
			}

			if ( ! app.fields.active ) {
				return;
			}

			var top    = app.fields.active.$el.offset().top - app.scroll.baseline;
			var bottom = top + app.fields.active.$el.outerHeight( true );

			// Scrolling down.
			if ( winScroll > bottom ) {
				app.fields.updateActive( app.fields.findNext() );
			}

			// Scrolling up.
			if ( winScroll < top ) {
				app.fields.updateActive( app.fields.findPrev() );
			}

			// Make sure the active field has a correct position. Safety net for fast scrolling.
			setTimeout( function() {
				if ( app.fields.active && ! app.fields.isAtBaseline( app.fields.active ) ) {
					app.fields.updateActive();
				}
			}, 100 );
		},
	};

	/**
	 * Controls fields and field items JS events mapping/unmapping (including key bindings).
	 *
	 * Part of mainClasses.Field and mainClasses.FieldItem.
	 * Not meant to be used directly.
	 *
	 * @since 1.0.0
	 *
	 * @mixin
	 */
	eventMapControl = {

		/**
		 * Field/FieldItem object.
		 *
		 * @since 1.0.0
		 */
		obj: null,

		/**
		 * Type of events to bind.
		 *
		 * Looks for corresponding key in eventMapControl.obj.
		 *
		 * @since 1.0.0
		 */
		eventType: null,

		/**
		 * Unmap individual global events upon Field/FieldItem activation.
		 *
		 * @since 1.0.0
		 */
		unmapDisabledEvents: function() {

			if ( helpers.object.isEmpty( this.obj[ this.eventType ].disable ) ) {
				return;
			}

			// TODO: Change $.each to something more performant.
			$.each( this.obj[ this.eventType ].disable, function( key ) {

				if ( typeof this.obj[ this.eventType ].disable[ key ] === 'undefined' ) {
					return true;
				}

				this.obj[ this.eventType ].disable[ key ].$el
					.off(
						this.obj[ this.eventType ].disable[ key ].handler,
						this.obj[ this.eventType ].disable[ key ].fn
					);
			}.bind( this ) );
		},

		/**
		 * Map previously unmapped global events on Field/FieldItem deactivation.
		 *
		 * @since 1.0.0
		 */
		mapDisabledEvents: function() {

			if ( helpers.object.isEmpty( this.obj[ this.eventType ].disable ) ) {
				return;
			}

			// TODO: Change $.each to something more performant.
			$.each( this.obj[ this.eventType ].disable, function( key ) {

				if ( typeof this.obj[ this.eventType ].disable[ key ] === 'undefined' ) {
					return true;
				}

				this.obj[ this.eventType ].disable[ key ].$el
					.on(
						this.obj[ this.eventType ].disable[ key ].handler,
						this.obj[ this.eventType ].disable[ key ].fn
					);
			}.bind( this ) );
		},

		/**
		 * Map Field/FieldItem specific events upon activation.
		 *
		 * @since 1.0.0
		 */
		mapEnabledEvents: function() {

			if ( helpers.object.isEmpty( this.obj[ this.eventType ].enable ) ) {
				return;
			}

			// TODO: Change $.each to something more performant.
			$.each( this.obj[ this.eventType ].enable, function( key ) {

				if ( this.obj[ this.eventType ].active[ key ] === 'undefined' ) {
					return true;
				}

				this.obj[ this.eventType ].active[ key ] = this.obj[ this.eventType ].enable[ key ];
				this.obj[ this.eventType ].active[ key ].fn = this.obj[ this.eventType ].enable[ key ].fn.bind( this.obj );

				this.obj[ this.eventType ].active[ key ].$el
					.on(
						this.obj[ this.eventType ].active[ key ].handler,
						this.obj[ this.eventType ].active[ key ].fn
					);
			}.bind( this ) );
		},

		/**
		 * Unmap Field/FieldItem specific events upon deactivation.
		 *
		 * @since 1.0.0
		 */
		unmapEnabledEvents: function() {

			if ( helpers.object.isEmpty( this.obj[ this.eventType ].active ) ) {
				return;
			}

			// TODO: Change $.each to something more performant.
			$.each( this.obj[ this.eventType ].active, function( key ) {

				if ( this.obj[ this.eventType ].active[ key ] === 'undefined' ) {
					return true;
				}

				this.obj[ this.eventType ].active[ key ].$el
					.off(
						this.obj[ this.eventType ].active[ key ].handler,
						this.obj[ this.eventType ].active[ key ].fn
					);
			}.bind( this ) );

			this.obj[ this.eventType ].active = {};
		},

		/**
		 * Entry point for Field/FieldItem activation.
		 *
		 * Unmap global and map Field/FieldItem specific events.
		 *
		 * @since 1.0.0
		 *
		 * @param {mainClasses.Field|mainClasses.FieldItem} obj Object to map/unmap events.
		 * @param {string} eventType Type of events to bind.
		 */
		mapEvents: function( obj, eventType ) {

			if ( ! obj || ! eventType ) {
				return;
			}

			if ( ! ( eventType in obj ) ) {
				return;
			}

			this.obj = obj;
			this.eventType = app.isMobileDevice() ? eventType + 'Mobile' : eventType;

			if ( helpers.object.isEmpty( this.obj[ this.eventType ] ) ) {
				return;
			}

			this.unmapDisabledEvents();
			this.mapEnabledEvents();
		},

		/**
		 * Entry point for Field/FieldItem deactivation.
		 *
		 * Map previously unmapped global and unmap Field/FieldItem specific events.
		 *
		 * @since 1.0.0
		 *
		 * @param {mainClasses.Field|mainClasses.FieldItem} obj Object to map/unmap events.
		 * @param {string} eventType Type of events to bind.
		 */
		unmapEvents: function( obj, eventType ) {

			if ( ! obj || ! eventType ) {
				return;
			}

			if ( ! ( eventType in obj ) ) {
				return;
			}

			this.obj = obj;
			this.eventType = app.isMobileDevice() ? eventType + 'Mobile' : eventType;

			if ( helpers.object.isEmpty( this.obj[ this.eventType ] ) ) {
				return;
			}

			this.unmapEnabledEvents();
			this.mapDisabledEvents();
		},
	};

	/**
	 * Main classes.
	 *
	 * @since 1.0.0
	 */
	mainClasses = {

		/**
		 * Field Item class.
		 *
		 * @since 1.0.0
		 *
		 * @mixes eventMapControl
		 */
		FieldItem: ( function() {

			/**
			 * FieldItem constructor.
			 *
			 * @since 1.0.0
			 *
			 * @param {jQuery} $el Main FieldItem element.
			 * @param {string} id Unique FieldItem key.
			 * @param {string} type Type of FieldItem.
			 * @param {mainClasses.Field} parentField Parent Field object.
			 *
			 * @class
			 */
			function FieldItem( $el, id, type, parentField ) {

				/**
				 * Main FieldItem element.
				 *
				 * @since 1.0.0
				 *
				 * @type {jQuery}
				 */
				this.$el = $el;

				/**
				 * Unique FieldItem key.
				 *
				 * @since 1.0.0
				 *
				 * @type {string}
				 */
				this.id = id;

				/**
				 * Type of FieldItem.
				 *
				 * @since 1.0.0
				 *
				 * @type {string}
				 */
				this.type = type;

				/**
				 * Parent Field object.
				 *
				 * @since 1.0.0
				 *
				 * @type {mainClasses.Field}
				 */
				this.parentField = parentField;

				/**
				 * FieldItem.$el contains cursor.
				 *
				 * @since 1.0.0
				 *
				 * @type {boolean}
				 */
				this.focusable = false;

				/**
				 * FieldItem.$el is visually highlighted.
				 *
				 * @since 1.0.0
				 *
				 * @type {boolean}
				 */
				this.highlighted = false;

				/**
				 * Keyboard JS events (keymap).
				 *
				 * @since 1.0.0
				 *
				 * @type {object}
				 */
				this.keyboard = {

					/**
					 * List of FieldItem specific keyboard events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					enable: {},

					/**
					 * List of global keyboard events to disable on FieldItem activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					disable: {},

					/**
					 * List of currently active FieldItem specific keyboard events.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					active: {},
				};

				/**
				 * General JS events.
				 *
				 * @since 1.0.0
				 *
				 * @type {object}
				 */
				this.events = {

					/**
					 * List of FieldItem specific general events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					enable: {},

					/**
					 * List of currently active FieldItem specific general events.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					active: {},
				};

				/**
				 * Keyboard JS events (keymap) for mobile.
				 *
				 * @since 1.1.0
				 *
				 * @type {object}
				 */
				this.keyboardMobile = {

					/**
					 * List of FieldItem specific mobile keyboard events to enable on activation.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					enable: {},

					/**
					 * List of global keyboard events to disable on FieldItem activation for mobile.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					disable: {},

					/**
					 * List of currently active FieldItem specific mobile keyboard events.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					active: {},
				};

				/**
				 * General JS events for mobile.
				 *
				 * @since 1.1.0
				 *
				 * @type {object}
				 */
				this.eventsMobile = {

					/**
					 * List of FieldItem specific mobile general events to enable on activation.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					enable: {},

					/**
					 * List of currently active FieldItem specific mobile general events.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					active: {},
				};

				/**
				 * Storage for temp FieldItem data.
				 *
				 * @since 1.0.0
				 *
				 * @type {object}
				 */
				this.vars = {};

				// Run init actions.
				this.init();
			}

			/**
			 * Map FieldItem specific general (not keyboard) events.
			 *
			 * This takes place of Field (not FieldItem) activation.
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.mapEvents = function() {

				this.events.enable.clickActivate = {

					$el    : this.$el,
					handler: 'mousedown',
					fn     : function( e ) {

						if ( ! this.parentField.items || ! this.parentField.items.current ) {
							return;
						}

						if ( typeof this.id === 'undefined' || ! ( this.id in this.parentField.items.registered ) ) {
							return;
						}

						if ( this.id === this.parentField.items.current.id ) {
							return;
						}

						var item = this.parentField.items.registered[ this.id ];

						// TODO: This logic has to be common for events.fieldItem.general.click and mainClasses.FieldItemsSet.highlightNext.
						this.parentField.items.current.fadeOut();
						this.parentField.items.setCurrent( item );
						app.scroll.animate( item.$el ).then( item.fadeIn.bind( this ) );
					},
				};

				this.eventsMobile.enable.clickActivate = this.events.enable.clickActivate;

				eventMapControl.mapEvents( this, 'events' );
			};

			/**
			 * Unmap FieldItem specific general (not keyboard) events.
			 *
			 * This takes place of Field (not FieldItem) deactivation.
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.unmapEvents = function() {

				eventMapControl.unmapEvents( this, 'events' );
			};

			/**
			 * Check if FieldItem.$el can contain a cursor.
			 *
			 * @since 1.0.0
			 *
			 * @returns {boolean} FieldItem.$el can contain a cursor.
			 */
			FieldItem.prototype.isFocusable = function() {

				var tagName = this.$el.prop( 'tagName' ),
					elementType = this.$el.prop( 'type' );

				if ( 'hidden' === elementType ) {
					return false;
				}

				// TODO: Maybe only type will suffice here.
				if ( 'INPUT' === tagName && [ 'checkbox', 'radio' ].indexOf( elementType ) < 0 ) {
					return true;
				}

				if ( 'TEXTAREA' === tagName ) {
					return true;
				}

				if ( 'SELECT' === tagName ) {
					return true;
				}

				return false;
			};

			/**
			 * Focus FieldItem.
			 *
			 * Keyboard events only (not general events) are mapped here.
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.focus = function() {

				if ( this.focusable && ! app.isMobileDevice() ) {
					this.$el.get( 0 ).focus( { preventScroll: true } );
				}

				eventMapControl.mapEvents( this, 'keyboard' );

				this.$el.trigger( 'wpformsConvFormsFieldItemFocus', this );
			};

			/**
			 * Blur FieldItem.
			 *
			 * Keyboard events only (not general events) are unmapped here.
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.blur = function() {

				if ( this.focusable ) {
					this.$el.blur();
				}

				eventMapControl.unmapEvents( this, 'keyboard' );

				this.$el.trigger( 'wpformsConvFormsFieldItemBlur', this );
			};

			/**
			 * Add HTML upon activation.
			 *
			 * Used for adding helper text.
			 * Designed to be overridden by FieldItem child classes.
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.addHTML = function() {};

			/**
			 * Remove HTML added by FieldItem.addHTML().
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.removeHTML = function() {

				this.parentField.$el.find( '.wpforms-conversational-field-item-additional-html' ).remove();
			};

			/**
			 * Get element to add hover class to.
			 *
			 * Designed to be overridden by FieldItem child classes with unique HTML structure.
			 *
			 * @since 1.0.0
			 *
			 * @returns {jQuery} Element to add hover class to.
			 */
			FieldItem.prototype.getHoverEl = function() {

				return this.$el;
			};

			/**
			 * Add hover class to an element.
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.addHover = function() {

				var $hoverEl = this.getHoverEl();

				if ( ! $hoverEl ) {
					return;
				}

				$hoverEl.addClass( 'wpforms-field-item-hover' );
			};

			/**
			 * Remove hover class from an element.
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.removeHover = function() {

				var $hoverEl = this.getHoverEl();

				if ( ! $hoverEl ) {
					return;
				}

				$hoverEl.removeClass( 'wpforms-field-item-hover' );
			};

			/**
			 * Visually highlight of focus an element.
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.fadeIn = function() {

				if ( ! this.highlighted && ! this.focusable ) {
					this.highlighted = true;
					this.addHover();
				}

				this.focus();

				if ( ! app.isMobileDevice() ) {
					this.addHTML();
				}
			};

			/**
			 * Remove visual highlight or focus from an element.
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.fadeOut = function() {

				if ( this.highlighted && ! this.focusable ) {
					this.highlighted = false;
					this.removeHover();
				}

				this.blur();

				if ( ! app.isMobileDevice() ) {
					this.removeHTML();
				}
			};

			/**
			 * Get element to be validated.
			 *
			 * Designed to be overridden by FieldItem child classes with unique HTML structure.
			 *
			 * @since 1.0.0
			 *
			 * @returns {jQuery} Element to validate.
			 */
			FieldItem.prototype.getValidateEl = function() {

				return this.$el;
			};

			/**
			 * Validate FieldItem.
			 *
			 * @since 1.0.0
			 *
			 * @param {jQuery.validator} validator jQuery Validate instance.
			 * @param {boolean} force Unconditionally validate FieldItem.
			 *
			 * @returns {boolean} FieldItem is valid.
			 */
			FieldItem.prototype.validate = function( validator, force ) {

				if ( typeof $.fn.validate === 'undefined' ) {
					return true;
				}

				if ( ! validator ) {
					validator = elements.form.data( 'validator' );
				}

				if ( ! validator ) {
					return true;
				}

				if ( this.focusable || force ) {
					return validator.element( this.getValidateEl() );
				}

				return true;
			};

			/**
			 * FieldItem init actions.
			 *
			 * @since 1.0.0
			 */
			FieldItem.prototype.init = function() {

				this.focusable = this.isFocusable();
			};

			return FieldItem;
		}() ),

		/**
		 * Field Items class.
		 *
		 * Part of mainClasses.Field.
		 * FieldItem management functionality abstracted into a separate class.
		 *
		 * @since 1.0.0
		 */
		FieldItemsSet: ( function() {

			/**
			 * FieldItemsSet constructor.
			 *
			 * @since 1.0.0
			 *
			 * @param {mainClasses.Field} field Field object.
			 *
			 * @class
			 */
			function FieldItemsSet( field ) {

				/**
				 * Field object
				 *
				 * @since 1.0.0
				 *
				 * @type {mainClasses.Field}
				 */
				this.field = field;

				/**
				 * List of registered FieldItems for FieldItemsSet.field.
				 *
				 * @since 1.0.0
				 *
				 * @type {object}
				 */
				this.registered = {};

				/**
				 * Currently operated FieldItem.
				 *
				 * @since 1.0.0
				 *
				 * @type {mainClasses.FieldItem}
				 */
				this.current = null;
			}

			/**
			 * Find elements within FieldItemsSet.field.$el to be registered as FieldItems.
			 *
			 * Designed to be overridden by FieldItemsSet child classes for Fields with unique HTML structure.
			 *
			 * @since 1.0.0
			 *
			 * @returns {jQuery} Set of found elements.
			 */
			FieldItemsSet.prototype.findElements = function() {

				return this.field.$el.find( 'input, textarea, select' );
			};

			/**
			 * Identify element type.
			 *
			 * Designed to be overridden by FieldItemsSet child classes for Fields with unique HTML structure.
			 *
			 * @since 1.0.0
			 *
			 * @param {jQuery} $el Element to inspect.
			 *
			 * @returns {string} Element type.
			 */
			FieldItemsSet.prototype.identifyItemType = function( $el ) {

				if ( ! $el && this.current ) {
					$el = this.current.$el;
				}

				if ( ! $el ) {
					return '';
				}

				return $el.prop( 'type' );
			};

			/**
			 * Turn elements found within FieldItemsSet.field.$el into a list of registered FieldItems.
			 *
			 * @since 1.0.0
			 */
			FieldItemsSet.prototype.updateRegistered = function() {

				if ( ! this.field.$el ) {
					return;
				}

				var elements = this.findElements();

				if ( ! elements.length ) {
					return;
				}

				elements.each( function( id, el ) {

					var itemId = this.field.id + '-' + id;
					var itemType = this.identifyItemType( $( el ) );

					if ( 'hidden' === itemType ) {
						return true;
					}

					var typeClass = helpers.string.toCapitalizedCamelCase( itemType );
					var FieldItemClass = mainClasses.FieldItem;

					if ( typeClass in childClasses.fieldItem ) {
						FieldItemClass = childClasses.fieldItem[ typeClass ];
					}

					if ( app.isMobileDevice() && 'SelectOne' === typeClass ) {
						FieldItemClass = childClasses.fieldItem.SelectMobile;
					}

					this.registered[ itemId ] = new FieldItemClass( $( el ), itemId, itemType, this.field );

				}.bind( this ) );
			};

			/**
			 * Set currently operated FieldItem.
			 *
			 * @since 1.0.0
			 *
			 * @param {mainClasses.FieldItem} item Item to set as current.
			 */
			FieldItemsSet.prototype.setCurrent = function( item ) {

				if ( this.current ) {
					this.current.fadeOut();
				}

				if ( ! item ) {
					this.current = null;
					return;
				}

				if ( item instanceof mainClasses.FieldItem ) {
					this.current = item;
				}
			};

			/**
			 * Set set first registered FieldItem as current.
			 *
			 * @since 1.0.0
			 */
			FieldItemsSet.prototype.setCurrentFirst = function() {

				var firstItem = helpers.object.findFirstKey( this.registered );

				if ( firstItem ) {
					this.setCurrent( firstItem );
				}
			};

			/**
			 * Set set last registered FieldItem as current.
			 *
			 * @since 1.0.0
			 */
			FieldItemsSet.prototype.setCurrentLast = function() {

				var lastItem = helpers.object.findLastKey( this.registered );

				if ( lastItem ) {
					this.setCurrent( lastItem );
				}
			};

			/**
			 * Initialize current FieldItem.
			 *
			 * @since 1.0.0
			 */
			FieldItemsSet.prototype.initCurrent = function() {

				if ( this.current ) {
					return;
				}

				var firstItem = helpers.object.findFirstKey( this.registered );

				if ( firstItem ) {
					this.setCurrent( firstItem );
				}
			};

			/**
			 * Select current FieldItem.
			 *
			 * @since 1.0.0
			 */
			FieldItemsSet.prototype.selectCurrent = function() {

				if ( ! this.current || ! ( this.current instanceof mainClasses.FieldItem ) ) {
					return;
				}

				this.current.$el.trigger( 'click' );
			};

			/**
			 * Check if currently operated FieldItem is visually highlighted.
			 *
			 * @since 1.0.0
			 *
			 * @returns {boolean} Current FieldItem is visually highlighted.
			 */
			FieldItemsSet.prototype.isCurrentHighlighted = function() {

				if ( ! this.current ) {
					return false;
				}

				return Boolean( this.current.highlighted );
			};

			/**
			 * Find FieldItem next to current.
			 *
			 * @since 1.0.0
			 *
			 * @returns {mainClasses.FieldItem} Next FieldItem.
			 */
			FieldItemsSet.prototype.findNext = function() {

				if ( ! this.current ) {
					return false;
				}

				var item = helpers.object.findNextKey( this.registered, this.current.id );

				while ( item && ! item.$el.is( ':visible' ) ) {
					item = helpers.object.findNextKey( this.registered, item.id );
				}

				return item;
			};

			/**
			 * Find FieldItem previous to current.
			 *
			 * @since 1.0.0
			 *
			 * @returns {mainClasses.FieldItem} Previous FieldItem.
			 */
			FieldItemsSet.prototype.findPrev = function() {

				if ( ! this.current ) {
					return false;
				}

				var item = helpers.object.findPrevKey( this.registered, this.current.id );

				while ( item && ! item.$el.is( ':visible' ) ) {
					item = helpers.object.findPrevKey( this.registered, item.id );
				}

				return item;
			};

			/**
			 * Attempt to highlight next FieldItem.
			 *
			 * @since 1.0.0
			 *
			 * @returns {jQuery.Deferred} jQuery object for callbacks.
			 */
			FieldItemsSet.prototype.highlightNext = function() {

				var promise = new $.Deferred();

				if ( ! this.current ) {
					return promise.reject();
				}

				if ( ! this.current.focusable && ! this.current.highlighted ) {
					this.setCurrentFirst();
					this.current.fadeIn();
					return promise.resolve();
				}

				var nextItem = this.findNext();

				if ( ! nextItem ) {
					return promise.reject();
				}

				if ( ! this.current.validate() ) {
					return promise.resolve();
				}

				this.current.fadeOut();

				this.current = nextItem;

				if ( this.current.focusable ) {
					app.scroll.animate( this.current.$el )
						.then( function() {
							this.current.fadeIn();
							promise.resolve();
						}.bind( this ) );
				} else {
					this.current.fadeIn();
					promise.resolve();
				}

				return promise;
			};

			/**
			 * Attempt to highlight previous FieldItem.
			 *
			 * @since 1.0.0
			 *
			 * @returns {jQuery.Deferred} jQuery object for callbacks.
			 */
			FieldItemsSet.prototype.highlightPrev = function() {

				var promise = new $.Deferred();

				if ( ! this.current ) {
					return promise.reject();
				}

				if ( ! this.current.focusable && ! this.current.highlighted ) {
					this.setCurrentLast();
					this.current.fadeIn();
					return promise.resolve();
				}

				var prevItem = this.findPrev();

				if ( ! prevItem ) {
					return promise.reject();
				}

				this.current.fadeOut();

				this.current = prevItem;

				if ( this.current.focusable ) {
					app.scroll.animate( this.current.$el )
						.then( function() {
							this.current.fadeIn();
							promise.resolve();
						}.bind( this ) );
				} else {
					this.current.fadeIn();
					promise.resolve();
				}

				return promise;
			};

			return FieldItemsSet;
		}() ),

		/**
		 * Field class.
		 *
		 * @since 1.0.0
		 *
		 * @mixes eventMapControl
		 */
		Field: ( function() {

			/**
			 * Field constructor.
			 *
			 * @since 1.0.0
			 *
			 * @param {jQuery} $el Main Field element.
			 * @param {string} id Unique Field key.
			 *
			 * @borrows mainClasses.FieldItemsSet as items.
			 *
			 * @class
			 */
			function Field( $el, id ) {

				/**
				 * Main Field element.
				 *
				 * @since 1.0.0
				 *
				 * @type {jQuery}
				 */
				this.$el = $el;

				/**
				 * Unique FieldItem key.
				 *
				 * @since 1.0.0
				 *
				 * @type {string}
				 */
				this.id = id;

				/**
				 * Type of Field.
				 *
				 * @since 1.0.0
				 *
				 * @type {string}
				 */
				this.type = null;

				/**
				 * FieldItem management functionality.
				 *
				 * @type {mainClasses.FieldItemsSet}
				 */
				this.items = null;

				/**
				 * Keyboard JS events (keymap).
				 *
				 * @since 1.0.0
				 *
				 * @type {object}
				 */
				this.keyboard = {

					/**
					 * List of Field specific keyboard events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					enable: {},

					/**
					 * List of global keyboard events to disable on Field activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					disable: {},

					/**
					 * List of currently active Field specific keyboard events.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					active: {},
				};

				/**
				 * General JS events.
				 *
				 * @since 1.0.0
				 *
				 * @type {object}
				 */
				this.events = {

					/**
					 * List of Field specific general events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					enable: {},

					/**
					 * List of currently active Field specific general events.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					active: {},
				};

				/**
				 * Keyboard JS events (keymap) for mobile.
				 *
				 * @since 1.1.0
				 *
				 * @type {object}
				 */
				this.keyboardMobile = {

					/**
					 * List of Field specific mobile keyboard events to enable on activation.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					enable: {},

					/**
					 * List of global keyboard events to disable on Field activation for mobile.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					disable: {},

					/**
					 * List of currently active Field specific mobile keyboard events.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					active: {},
				};

				/**
				 * General JS events for mobile.
				 *
				 * @since 1.1.0
				 *
				 * @type {object}
				 */
				this.eventsMobile = {

					/**
					 * List of Field specific mobile general events to enable on activation.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					enable: {},

					/**
					 * List of currently active Field specific mobile general events.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					active: {},
				};

				/**
				 * Storage for temp Field data.
				 *
				 * @since 1.0.0
				 *
				 * @type {object}
				 */
				this.vars = {};

				// Run init actions.
				this.init();

			}

			/**
			 * Maybe unmap some global events and map both keyboard and general Field specific events.
			 *
			 * FieldItem general (not keyboard) events are also mapped here.
			 *
			 * @since 1.0.0
			 */
			Field.prototype.mapEvents = function() {

				eventMapControl.mapEvents( this, 'keyboard' );
				eventMapControl.mapEvents( this, 'events' );

				$.each( this.items.registered, function( id, item ) {
					item.mapEvents();
				} );
			};

			/**
			 * Map previously unmapped global and unmap both keyboard and general Field specific events.
			 *
			 * FieldItem general (not keyboard) events are also mapped here.
			 *
			 * @since 1.0.0
			 */
			Field.prototype.unmapEvents = function() {

				eventMapControl.unmapEvents( this, 'keyboard' );
				eventMapControl.unmapEvents( this, 'events' );

				$.each( this.items.registered, function( id, item ) {
					item.unmapEvents();
				} );
			};

			/**
			 * Focus Field.
			 *
			 * @since 1.0.0
			 */
			Field.prototype.focus = function() {

				if ( this.items.current ) {
					this.items.current.focus();
				}
			};

			/**
			 * Blur Field.
			 *
			 * @since 1.0.0
			 */
			Field.prototype.blur = function() {

				if ( this.items.current ) {
					this.items.current.fadeOut();
				}
			};

			/**
			 * Add HTML upon activation.
			 *
			 * Used for adding helper text.
			 * Designed to be overridden by Field child classes.
			 *
			 * @since 1.0.0
			 */
			Field.prototype.addHTML = function() {};

			/**
			 * Remove HTML added by Field.addHTML().
			 *
			 * @since 1.0.0
			 */
			Field.prototype.removeHTML = function() {

				this.$el.find( '.wpforms-conversational-field-additional-html' ).remove();
			};

			/**
			 * Deactivate Field.
			 *
			 * @since 1.0.0
			 */
			Field.prototype.deactivate = function() {

				this.$el.removeClass( 'wpforms-conversational-form-field-active' );

				this.blur();

				this.removeHTML();

				this.unmapEvents();
			};

			/**
			 * Activate Field.
			 *
			 * @since 1.0.0
			 */
			Field.prototype.activate = function() {

				if ( this.$el.hasClass( 'wpforms-conversational-form-field-active' ) ) {
					return;
				}

				this.$el.trigger( 'wpformsConvFormsFieldActivationBefore', this );

				this.$el.addClass( 'wpforms-conversational-form-field-active' );

				this.mapEvents();

				if ( ! app.isMobileDevice() ) {
					this.addHTML();
				}

				this.focus();

				this.$el.trigger( 'wpformsConvFormsFieldActivationAfter', this );
			};

			/**
			 * Validate Field.
			 *
			 * @since 1.0.0
			 *
			 * @returns {boolean} Field is valid.
			 */
			Field.prototype.validate = function() {

				if ( typeof $.fn.validate === 'undefined' ) {
					return true;
				}

				var validator = elements.form.data( 'validator' );

				if ( ! validator ) {
					return true;
				}

				var invalidIds = [];

				$.each( this.items.registered, function( id, item ) {
					if ( ! item.validate( validator, true ) ) {
						invalidIds.push( id );
					}
				} );

				this.$el.trigger( 'wpformsConvFormsFieldValidation', [ invalidIds, this ] );

				return ! invalidIds.length;
			};

			/**
			 * Get FieldItemsSet object.
			 *
			 * Designed to be overridden by Field child classes.
			 *
			 * @since 1.0.0
			 *
			 * @returns {mainClasses.FieldItemsSet} FieldItemsSet object.
			 */
			Field.prototype.getFieldItemsSetObj = function() {

				var fieldItemsSetClassName = helpers.string.toCapitalizedCamelCase( this.type );
				var FieldItemsSet;

				if ( fieldItemsSetClassName in childClasses.fieldItemsSet ) {
					FieldItemsSet = childClasses.fieldItemsSet[ fieldItemsSetClassName ];
				} else {
					FieldItemsSet = mainClasses.FieldItemsSet;
				}

				return new FieldItemsSet( this );
			};

			/**
			 * Field init actions.
			 *
			 * @since 1.0.0
			 */
			Field.prototype.init = function() {

				this.type = this.$el.data( 'field-type' );

				this.items = this.getFieldItemsSetObj();

				this.items.updateRegistered();

				this.items.initCurrent();
			};

			return Field;
		}() ),

		/**
		 * Fields Set class.
		 *
		 * Main point of entry for Fields manipulations.
		 *
		 * @since 1.0.0
		 */
		FieldsSet: ( function() {

			/**
			 * FieldsSet constructor.
			 *
			 * @since 1.0.0
			 *
			 * @class
			 */
			function FieldsSet() {

				/**
				 * List of registered Fields in a form.
				 *
				 * @since 1.0.0
				 *
				 * @type {object}
				 */
				this.registered = {};

				/**
				 * Currently active Field.
				 *
				 * @since 1.0.0
				 *
				 * @type {mainClasses.Field}
				 */
				this.active = null;
			}

			/**
			 * Turn form elements into a list of registered Fields.
			 *
			 * @since 1.0.0
			 */
			FieldsSet.prototype.updateRegistered = function() {

				elements.fields.each( function( i, el ) {

					var fieldId = $( el ).data( 'field-id' );
					var fieldType = $( el ).data( 'field-type' );

					if ( typeof fieldId === 'undefined' || ! fieldType ) {
						return true;
					}

					if ( 'hidden' === fieldType ) {
						return true;
					}

					var id = fieldId + '-' + fieldType;

					var typeClass = helpers.string.toCapitalizedCamelCase( fieldType );

					if ( 'PaymentMultiple' === typeClass ) {
						typeClass = 'Radio';
					}

					if ( [ 'PaymentCheckbox', 'GdprCheckbox' ].indexOf( typeClass ) !== -1 ) {
						typeClass = 'Checkbox';
					}

					if ( typeClass in childClasses.field ) {
						this.registered[ id ] = new childClasses.field[ typeClass ]( $( el ), id );
						return true;
					}

					this.registered[ id ] = new mainClasses.Field( $( el ), id );
				}.bind( this ) );
			};

			/**
			 * Check if Field or an element covers the baseline (see scrollControl.baseline).
			 *
			 * @since 1.0.0
			 *
			 * @param {mainClasses.Field|jQuery} field Field or element to inspect.
			 *
			 * @returns {boolean} Field or element is at the baseline.
			 */
			FieldsSet.prototype.isAtBaseline = function( field ) {

				// Nothing is at the baseline if the starting screen is active.
				if ( elements.page.hasClass( 'wpforms-conversational-form-start' ) ) {
					return false;
				}

				var $el = field;

				if ( ! $el ) {
					return false;
				}

				if ( field instanceof mainClasses.Field ) {
					$el = field.$el;
				}

				if ( ! $el.is( ':visible' ) ) {
					return false;
				}

				var scrollPos = $( window ).scrollTop() + app.scroll.baseline,
					top       = $el.offset().top,
					bottom    = top + $el.outerHeight( true );

				if ( scrollPos >= top && scrollPos <= bottom ) {
					return true;
				}

				return false;
			};

			/**
			 * Detect which one of the registered Fields covers the baseline (see scrollControl.baseline).
			 *
			 * @since 1.0.0
			 *
			 * @returns {mainClasses.Field} Field detected.
			 */
			FieldsSet.prototype.detectActive = function() {

				var fieldFound;

				$.each( this.registered, function( id, field ) {

					if ( this.isAtBaseline( field ) ) {
						fieldFound = field;
						return false;
					}
				}.bind( this ) );

				return fieldFound;
			};

			/**
			 * Set a Field as active.
			 *
			 * If no Field is passed as 'field' param it's detected automatically.
			 *
			 * @since 1.0.0
			 *
			 * @param {mainClasses.Field} field Field to activate.
			 * @param {boolean} shortCycle Short cycle only applies when an app (not user) scrolls the page (see scrollControl.to).
			 */
			FieldsSet.prototype.updateActive = function( field, shortCycle ) {

				this.clearActive();

				field = field || this.detectActive();

				if ( field && field instanceof mainClasses.Field && field.$el.length ) {

					// TODO: Event trigger is needed here.
					this.active = field;

					if ( ! shortCycle ) {
						field.activate();
					}
				}

				// TODO: Needs to be in app var connected with a hook.
				if ( ! shortCycle ) {
					app.updateProgressBar();
				}
			};

			/**
			 * Clear currently active field.
			 *
			 * @since 1.0.0
			 */
			FieldsSet.prototype.clearActive = function() {

				if ( this.active && this.active instanceof mainClasses.Field ) {

					// TODO: Event trigger is needed here.

					this.active.deactivate();
					this.active = null;
				}
			};

			// TODO: This method should go separate for app.fields and app.fields.items.
			/**
			 * Call a method on a currently active Field.
			 *
			 * @since 1.0.0
			 *
			 * @param {string} method Method to call.
			 *
			 * @returns {*} Call result.
			 */
			FieldsSet.prototype.callOnActive = function( method ) {

				if ( ! this.active || ! ( this.active instanceof mainClasses.Field ) ) {
					return;
				}

				var module;

				if ( method in this.active ) {
					module = this.active;
				}

				if ( method in this.active.items ) {
					module = this.active.items;
				}

				if ( ! module ) {
					return;
				}

				if ( ! ( module[ method ] instanceof Function ) ) {
					return;
				}

				return module[ method ]();
			};

			/**
			 * Find next Field.
			 *
			 * @since 1.0.0
			 *
			 * @returns {mainClasses.Field} Field found.
			 */
			FieldsSet.prototype.findNext = function() {

				if ( ! this.active ) {
					return;
				}

				var field = helpers.object.findNextKey( this.registered, this.active.id );

				while ( field && ! field.$el.is( ':visible' ) ) {
					field = helpers.object.findNextKey( this.registered, field.id );
				}

				return field;
			};

			/**
			 * Find previous Field.
			 *
			 * @since 1.0.0
			 *
			 * @returns {mainClasses.Field} Field found.
			 */
			FieldsSet.prototype.findPrev = function() {

				if ( ! this.active ) {
					return;
				}

				var field = helpers.object.findPrevKey( this.registered, this.active.id );

				while ( field && ! field.$el.is( ':visible' ) ) {
					field = helpers.object.findPrevKey( this.registered, field.id );
				}

				return field;
			};

			/**
			 * Get visible fields list.
			 *
			 * @since 1.2.0
			 *
			 * @returns {object} Visible fields list.
			 */
			FieldsSet.prototype.getVisible = function() {

				var visible = {};

				$.each( this.registered, function( id, field ) {
					if ( field.$el.is( ':visible' ) ) {
						visible[ id ] = field;
					}
				} );

				return visible;
			};

			/**
			 * Get completed (scroll based) fields count.
			 *
			 * @since 1.2.0
			 *
			 * @param {object} visibleFields Subset of visible fields (optional).
			 *
			 * @returns {number} Completed fields count.
			 */
			FieldsSet.prototype.getCompletedCount = function( visibleFields ) {

				var completedCount;

				if ( this.isAtBaseline( elements.header ) ) {
					return 0;
				}

				visibleFields = visibleFields || this.getVisible();

				if ( this.isAtBaseline( elements.footer ) ) {
					return Object.keys( visibleFields ).length;
				}

				if ( this.active ) {
					completedCount = helpers.object.getNumKeyIndex( visibleFields, this.active.id );
				}

				if ( 'undefined' !== typeof completedCount && '-1' !== completedCount ) {
					completedCount++;
				}

				return completedCount || 0;
			};

			/**
			 * Get completed (scroll based) fields percent.
			 *
			 * @since 1.2.0
			 *
			 * @param {object} visibleFields Subset of visible fields (optional).
			 * @param {number} completedCount Completed fields count (optional).
			 *
			 * @returns {number} Field percent.
			 */
			FieldsSet.prototype.getCompletedPercent = function( visibleFields, completedCount ) {

				var progress;

				if ( this.isAtBaseline( elements.header ) ) {
					return 0;
				}

				if ( this.isAtBaseline( elements.footer ) ) {
					return 100;
				}

				visibleFields  = visibleFields || this.getVisible();
				completedCount = completedCount || this.getCompletedCount( visibleFields );

				if ( 'undefined' !== typeof completedCount && '-1' !== completedCount ) {
					progress = Math.floor( ( completedCount * 100 ) / Object.keys( visibleFields ).length );
				}

				return progress || 0;
			};

			return FieldsSet;
		}() ),
	};


	/**
	 * Child classes.
	 *
	 * @since 1.0.0
	 */
	childClasses = {

		/**
		 * FieldItem child classes.
		 *
		 * @since 1.0.0
		 */
		fieldItem: {

			/**
			 * Checkbox FieldItem child class.
			 *
			 * @since 1.0.0
			 *
			 * @type {object}
			 */
			Checkbox: ( function() {

				/**
				 * Checkbox FieldItem constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main FieldItem element.
				 * @param {string} id Unique FieldItem key.
				 * @param {string} type Type of FieldItem.
				 * @param {mainClasses.Field} parentField Parent Field object.
				 *
				 * @class
				 */
				function Checkbox( $el, id, type, parentField ) {

					mainClasses.FieldItem.call( this, $el, id, type, parentField );
				}

				/**
				 * Get element to add hover class to.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 *
				 * @returns {jQuery} Element to add hover class to.
				 */
				Checkbox.prototype.getHoverEl = function() {

					return this.$el.closest( 'li' );
				};

				return Checkbox;
			}() ),

			/**
			 * Radio FieldItem child class.
			 *
			 * @since 1.0.0
			 *
			 * @type {object}
			 */
			Radio: ( function() {

				/**
				 * Radio FieldItem constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main FieldItem element.
				 * @param {string} id Unique FieldItem key.
				 * @param {string} type Type of FieldItem.
				 * @param {mainClasses.Field} parentField Parent Field object.
				 *
				 * @class
				 */
				function Radio( $el, id, type, parentField ) {

					mainClasses.FieldItem.call( this, $el, id, type, parentField );

					/**
					 * List of Radio FieldItem specific general events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.events.enable = {

						change: {

							$el: this.$el,
							handler: 'change',
							fn: app.scroll.next,
						},
					};

					/**
					 * List of Radio FieldItem specific mobile general events to enable on activation.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					this.eventsMobile.enable = {

						change: this.events.enable.change,
					};
				}

				/**
				 * Get element to add hover class to.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 *
				 * @returns {jQuery} Element to add hover class to.
				 */
				Radio.prototype.getHoverEl = function() {

					var $hoverEl;

					if ( [ 'radio', 'payment-multiple' ].indexOf( this.parentField.type ) !== -1 ) {
						$hoverEl = this.$el.closest( 'li' );
					}

					if ( 'rating' === this.parentField.type ) {
						$hoverEl = this.$el.parent( 'label' );
					}

					if ( 'net_promoter_score' === this.parentField.type ) {
						$hoverEl = this.$el.siblings( 'label' );
					}

					return $hoverEl;
				};

				return Radio;
			}() ),

			/**
			 * SelectOne FieldItem child class.
			 *
			 * @since 1.0.0
			 *
			 * @type {object}
			 */
			SelectOne: ( function() {

				/**
				 * SelectOne FieldItem constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main FieldItem element.
				 * @param {string} id Unique FieldItem key.
				 * @param {string} type Type of FieldItem.
				 * @param {mainClasses.Field} parentField Parent Field object.
				 *
				 * @class
				 */
				function SelectOne( $el, id, type, parentField ) {

					/**
					 * List of SelectOne FieldItem specific elements.
					 *
					 * @type {object}
					 */
					this.elements = {};

					mainClasses.FieldItem.call( this, $el, id, type, parentField );

					/**
					 * List of global keyboard events to disable on SelectOne FieldItem activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.disable = {

						up: app.globalEvents.keyboard.up,
						down: app.globalEvents.keyboard.down,
						enter: app.globalEvents.keyboard.enter,
						space: app.globalEvents.keyboard.space,
					};

					/**
					 * List of SelectOne FieldItem specific keyboard events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.enable = {

						up: {

							$el    : $( window ),
							handler: 'keydown',
							fn     : function( e ) {

								if ( 38 !== e.keyCode ) {
									return;
								}

								if ( ! this.dropdownIsOpened() ) {

									try {
										app.fields.active.items.highlightPrev().fail( app.scroll.prev );
									} catch ( e ) {
										app.scroll.prev();
									}
									return;
								}

								e.preventDefault();

								var $traversableItems = this.elements.$items.filter( ':visible' );
								var $selected = $traversableItems.filter( function() {
									return $( this ).hasClass( 'selected' );
								} );

								$traversableItems.removeClass( 'selected' );

								var $prev;

								if ( ! $selected.length ) {
									$prev = $traversableItems.last().addClass( 'selected' );
								}

								if ( ! $prev ) {
									$prev = $selected.prevAll( '.wpforms-conversational-form-dropdown-item:visible' );
								}

								if ( ! $prev.length ) {
									$prev = $traversableItems.last();
								}

								this.scrollItemIntoView( $prev );

								$prev.first().addClass( 'selected' );
							},
						},

						down: {

							$el    : $( window ),
							handler: 'keydown',
							fn     : function( e ) {

								if ( 40 !== e.keyCode ) {
									return;
								}

								e.preventDefault();

								if ( ! this.dropdownIsOpened() ) {

									this.dropdownOpen( true );
									return;
								}

								var $traversableItems = this.elements.$items.filter( ':visible' );
								var $selected = $traversableItems.filter( function() {
									return $( this ).hasClass( 'selected' );
								} );

								$traversableItems.removeClass( 'selected' );

								var $next;

								if ( ! $selected.length ) {
									$next = $traversableItems.first().addClass( 'selected' );
								}

								if ( ! $next ) {
									$next = $selected.nextAll( '.wpforms-conversational-form-dropdown-item:visible' );
								}

								if ( ! $next.length ) {
									$next = $traversableItems.first();
								}

								this.scrollItemIntoView( $next );

								$next.first().addClass( 'selected' );

							},
						},

						enter: {

							$el    : $( window ),
							handler: 'keydown',
							fn     : function( e ) {

								if ( 13 !== e.keyCode ) {
									return;
								}

								e.preventDefault();

								if ( ! this.dropdownIsOpened() ) {

									app.scroll.next();
									return;
								}

								var $selected = this.elements.$items.filter( '.selected:visible' );

								if ( ! $selected || ! $selected.length ) {
									$selected = this.elements.$items.filter( ':visible' );
								}

								if ( ! $selected || ! $selected.length ) {
									this.dropdownClose();
									return;
								}

								$selected.first().trigger( 'click' );
							},
						},

						esc: {

							$el    : $( window ),
							handler: 'keydown',
							fn     : function( e ) {

								if ( 27 === e.keyCode ) {
									this.dropdownClose();
								}
							},
						},
					};

					/**
					 * List of SelectOne FieldItem specific general events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.events.enable = {

						keyboardInput: {

							$el: this.$el,
							handler: 'keyup',
							fn: function( e ) {

								// TODO: Only proceed for alphanumerical keys of if input value is changed.
								if ( [ 13, 27, 38, 40 ].indexOf( e.keyCode ) !== -1 ) {
									return;
								}
								this.dropdownOpen();
							},
						},

						optionClick: {

							$el: this.elements.$items,
							handler: 'click',
							fn: function( e ) {

								var value = $( e.target ).data( 'value' );

								var results = this.elements.$options.filter( function( i, item ) {
									return $( item ).val().toString() === value.toString();
								} );

								if ( results.length ) {

									this.$el.val( results.first().text() );
									this.elements.$select.val( results.first().val() ).trigger( 'change' );

									this.elements.$selected = results.first();
								}

								this.dropdownClose();

								try {
									app.fields.active.items.highlightNext().fail( app.scroll.next );
								} catch ( e ) {
									app.scroll.next();
								}
							},
						},

						chevronClick: {

							$el: this.$el.siblings( '.fa-chevron-down' ),
							handler: 'click',
							fn: function() {

								if ( this.dropdownIsOpened() ) {
									this.dropdownClose();
									return;
								}

								this.dropdownOpen( true );
							},
						},

						blur: {

							$el: this.$el,
							handler: 'wpformsConvFormsFieldItemBlur',
							fn: function() {

								this.dropdownClose();
							},
						},
					};
				}

				/**
				 * Get element to be validated.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 *
				 * @returns {jQuery} Element to validate.
				 */
				SelectOne.prototype.getValidateEl = function() {

					return this.elements.$select;
				};

				/**
				 * FieldItem init actions.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 */
				SelectOne.prototype.init = function() {

					this.dropdownInit();

					// Change main element to be a text input instead of select.
					this.$el = this.elements.$container.find( '.wpforms-conversational-form-dropdown-input input' );

					this.dropdownPopulateInitialValue();

					this.type = 'select-input';

					this.focusable = this.isFocusable();
				};

				/**
				 * Dropdown element init actions.
				 *
				 * @since 1.0.0
				 */
				SelectOne.prototype.dropdownInit = function() {

					// TODO: HTML has to be served from PHP.
					this.$el.wrap( $( '<div></div>' )
						.addClass( 'wpforms-conversational-select' )
						.addClass( this.$el.attr( 'disabled' ) ? 'disabled' : '' )
					).before( '<div class="wpforms-conversational-form-dropdown-input">' +
						'<input type="text" class="wpforms-field-medium">' +
						'<i class="fa fa-chevron-down"></i></div>' +
						'<div class="wpforms-conversational-form-dropdown-list-empty">' + wpforms_conversational_forms.i18n.select_list_empty + '</div>' +
						'<ul class="wpforms-conversational-form-dropdown-list"></ul>' +
						'<div class="wpforms-conversational-form-dropdown-list-helper">' + wpforms_conversational_forms.i18n.select_option_helper + '</div>'
					).css( {
						height: 0,
						width: 0,
						padding: 0,
						border: 0,
						display: 'block',
					} );

					if ( ! this.$el.find( '[selected]' ).length ) {
						this.$el.prepend( '<option value="" class="placeholder" disabled selected="selected">' + wpforms_conversational_forms.i18n.select_placeholder + '</option>' );
					}

					this.elements = {
						$select   : this.$el,
						$container: this.$el.parent( '.wpforms-conversational-select' ),
						$options  : this.$el.find( 'option' ),
						$selected : this.$el.find( 'option:selected' ),
					};

					this.elements.$itemList = this.elements.$container.find( '.wpforms-conversational-form-dropdown-list' );
					this.elements.$listEmpty = this.elements.$container.find( '.wpforms-conversational-form-dropdown-list-empty' ).hide();
					this.elements.$listHelper = this.elements.$container.find( '.wpforms-conversational-form-dropdown-list-helper' ).hide();

					this.elements.$options.each( function( i, option ) {
						var $option = $( option );

						if ( $option.hasClass( 'placeholder' ) ) {
							return true;
						}

						this.elements.$container.find( 'ul' ).append( $( '<li></li>' )
							.attr( 'data-value', $option.val() )
							.addClass( 'wpforms-conversational-form-dropdown-item' )
							.addClass( 'option' +
								( $option.is( ':selected' ) ? ' selected' : '' ) +
								( $option.is( ':disabled' ) ? ' disabled' : '' ) )
							.html( $option.text() )
						);
					}.bind( this ) );

					this.elements.$items = this.elements.$itemList.find( '.wpforms-conversational-form-dropdown-item' );
				};

				/**
				 * Put a value from original 'select' element into an input.
				 *
				 * @since 1.0.0
				 */
				SelectOne.prototype.dropdownPopulateInitialValue = function() {

					if ( ! this.elements.$selected.hasClass( 'placeholder' ) ) {
						this.$el.val( this.elements.$selected.text() );
					} else {
						this.$el.attr( 'placeholder', this.elements.$selected.text() );
					}
				};

				/**
				 * Filter dropdown elements containing a string.
				 *
				 * @since 1.0.0
				 *
				 * @param {string} search Search string.
				 *
				 * @returns {jQuery|false} Set of elements of false.
				 */
				SelectOne.prototype.dropdownFilter = function( search ) {

					try {
						var regex = new RegExp( search, 'gi' );
					} catch ( e ) {
						return false;
					}

					return this.elements.$items.filter( function( i, item ) {
						return $( item ).text().match( regex );
					} );
				};

				/**
				 * Open dropdown.
				 *
				 * @since 1.0.0
				 *
				 * @param {boolean} showAll Show all entries.
				 */
				SelectOne.prototype.dropdownOpen = function( showAll ) {

					var text = this.$el.val();
					var $results;

					this.elements.$listEmpty.hide();
					this.elements.$items.hide();

					$results = ( ! showAll && text ) ? this.dropdownFilter( text ) : this.elements.$items;

					if ( ! $results.length ) {
						this.elements.$itemList.addClass( 'opened' );
						this.elements.$listHelper.hide();
						this.elements.$listEmpty.show();
						return;
					}

					$results.show();

					this.elements.$listHelper.show();
					this.elements.$itemList.addClass( 'opened' );

					this.elements.$items.removeClass( 'selected' );
					$results.first().addClass( 'selected' );

					this.elements.$itemList.scrollTop( 0 );

					// TODO: Change CSS manipulations to a class toggle.
					$( 'body' ).css( 'overflow', 'hidden' );
					$( '#wpforms-conversational-form-page' ).css( 'paddingRight', '15px' );
				};

				/**
				 * Close dropdown.
				 *
				 * @since 1.0.0
				 */
				SelectOne.prototype.dropdownClose = function() {

					var text = this.$el.val();

					this.elements.$listHelper.hide();
					this.elements.$listEmpty.hide();

					if ( text ) {

						var results = this.elements.$options.filter( function( i, item ) {
							return $( item ).text() === text;
						} );

						if ( results.length ) {
							this.elements.$select.val( results.first().val() );
							this.elements.$selected = results.first();
						} else {
							this.$el.val( '' );
							this.dropdownPopulateInitialValue();
						}

					} else {

						this.dropdownPopulateInitialValue();
					}

					this.elements.$itemList.hide().removeClass( 'opened' );

					// TODO: Change CSS manipulations to a class toggle.
					$( 'body' ).css( 'overflow', 'auto' );
					$( '#wpforms-conversational-form-page' ).css( 'paddingRight', 'initial' );

					setTimeout( function() {
						this.elements.$itemList.show();
					}.bind( this ), 250 );

				};

				/**
				 * Check if dropdown is open.
				 *
				 * @since 1.0.0
				 *
				 * @returns {boolean} Dropdown is open.
				 */
				SelectOne.prototype.dropdownIsOpened = function() {

					if ( ! this.elements.$itemList || ! this.elements.$itemList.length ) {
						return false;
					}

					return this.elements.$itemList.hasClass( 'opened' );
				};

				/**
				 * Scroll dropdown item into view if it's covered by internal dropdown list scroll.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $item Dropdown list item.
				 */
				SelectOne.prototype.scrollItemIntoView = function( $item ) {

					$item = $item.first();

					if ( ! $item || ! $item.length ) {
						return;
					}

					var listHeight = this.elements.$itemList.height(),
						listScrollPos = this.elements.$itemList.scrollTop(),
						listPaddingTop = parseInt( this.elements.$itemList.css( 'padding-top' ), 10 );

					var itemHeight = $item.outerHeight(),
						itemRelativePos = $item.position().top,
						itemScrollPos = listScrollPos + itemRelativePos;

					if ( itemRelativePos < 0 ) {
						this.elements.$itemList.scrollTop( itemScrollPos - listPaddingTop );
					}

					if ( ( itemRelativePos + itemHeight ) > listHeight ) {
						this.elements.$itemList.scrollTop( itemScrollPos - ( listHeight - itemHeight ) );
					}
				};

				return SelectOne;
			}() ),

			/**
			 * SelectMobile FieldItem child class.
			 *
			 * @since 1.1.0
			 *
			 * @type {object}
			 */
			SelectMobile: ( function() {

				/**
				 * SelectMobile FieldItem constructor.
				 *
				 * @since 1.1.0
				 *
				 * @param {jQuery} $el Main FieldItem element.
				 * @param {string} id Unique FieldItem key.
				 * @param {string} type Type of FieldItem.
				 * @param {mainClasses.Field} parentField Parent Field object.
				 *
				 * @class
				 */
				function SelectMobile( $el, id, type, parentField ) {

					mainClasses.FieldItem.call( this, $el, id, type, parentField );

					/**
					 * List of SelectMobile FieldItem specific general events to enable on activation.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					this.eventsMobile.enable = {

						change: {

							$el    : this.$el,
							handler: 'change',
							fn     : function() {

								try {
									app.fields.active.items.highlightNext().fail( app.scroll.next );
								} catch ( e ) {
									app.scroll.next();
								}
							},
						},
					};
				}

				return SelectMobile;
			}() ),

			/**
			 * Url FieldItem child class.
			 *
			 * @since 1.0.0
			 *
			 * @type {object}
			 */
			Url: ( function() {

				/**
				 * Url FieldItem constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main FieldItem element.
				 * @param {string} id Unique FieldItem key.
				 * @param {string} type Type of FieldItem.
				 * @param {mainClasses.Field} parentField Parent Field object.
				 *
				 * @class
				 */
				function Url( $el, id, type, parentField ) {

					mainClasses.FieldItem.call( this, $el, id, type, parentField );
				}

				/**
				 * Get element to be validated.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 *
				 * @returns {jQuery} Element to validate.
				 */
				Url.prototype.getValidateEl = function() {

					// Trigger 'change' event to run URL 'http://' prefix completion before validation kicks in.
					this.$el.trigger( 'change' );

					return this.$el;
				};

				return Url;
			}() ),

			/**
			 * Date FieldItem child class.
			 *
			 * @since 1.0.0
			 *
			 * @type {object}
			 */
			Date: ( function() {

				/**
				 * Date FieldItem constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main FieldItem element.
				 * @param {string} id Unique FieldItem key.
				 * @param {string} type Type of FieldItem.
				 * @param {mainClasses.Field} parentField Parent Field object.
				 *
				 * @class
				 */
				function Date( $el, id, type, parentField ) {

					mainClasses.FieldItem.call( this, $el, id, type, parentField );

					this.$el.attr( 'data-rule-wpforms-conversational-forms-date', 'true' );

					this.loadInputMask();
				}

				/**
				 * Load date input mask.
				 *
				 * @since 1.0.0
				 */
				Date.prototype.loadInputMask = function() {

					if ( typeof $.fn.inputmask === 'undefined' ) {
						return;
					}

					var dateFormat = this.$el.data( 'date-format' );
					var dateInputFormat;

					switch ( dateFormat ) {
					case 'd/m/Y':
						dateInputFormat = 'dd/mm/yyyy';
						break;

					default:
						dateInputFormat = 'mm/dd/yyyy';
					}

					var dateInputArgs = Object.create( null );

					$.extend( dateInputArgs, { alias: 'datetime', inputFormat: dateInputFormat } );

					this.$el.inputmask( dateInputArgs );
				};

				return Date;
			}() ),

			/**
			 * Time FieldItem child class.
			 *
			 * @since 1.0.0
			 *
			 * @type {object}
			 */
			Time: ( function() {

				/**
				 * Time FieldItem constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main FieldItem element.
				 * @param {string} id Unique FieldItem key.
				 * @param {string} type Type of FieldItem.
				 * @param {mainClasses.Field} parentField Parent Field object.
				 *
				 * @class
				 */
				function Time( $el, id, type, parentField ) {

					mainClasses.FieldItem.call( this, $el, id, type, parentField );

					this.loadInputMask();
				}

				/**
				 * Load time input mask.
				 *
				 * @since 1.0.0
				 */
				Time.prototype.loadInputMask = function() {

					if ( typeof $.fn.inputmask === 'undefined' ) {
						return;
					}

					var timeFormat = this.$el.data( 'time-format' );
					var timeInputFormat;

					switch ( timeFormat ) {
					case 'H:i':
						timeInputFormat = 'HH:MM';
						break;
					default:
						timeInputFormat = 'hh:MM TT';
					}

					var timeInputArgs = Object.create( null );

					$.extend( timeInputArgs, { alias: 'datetime', inputFormat: timeInputFormat, placeholder: '_' } );

					this.$el.inputmask( timeInputArgs );
				};

				return Time;
			}() ),

			/**
			 * LikertRow FieldItem child class.
			 *
			 * @since 1.0.0
			 *
			 * @type {object}
			 */
			LikertRow: ( function() {

				/**
				 * LikertRow FieldItem constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main FieldItem element.
				 * @param {string} id Unique FieldItem key.
				 * @param {string} type Type of FieldItem.
				 * @param {mainClasses.Field} parentField Parent Field object.
				 *
				 * @class
				 */
				function LikertRow( $el, id, type, parentField ) {

					mainClasses.FieldItem.call( this, $el, id, type, parentField );
				}

				/**
				 * Validate FieldItem.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery.validator} validator jQuery Validate instance.
				 *
				 * @override
				 *
				 * @returns {boolean} FieldItem is valid.
				 */
				LikertRow.prototype.validate = function( validator ) {

					if ( typeof $.fn.validate === 'undefined' ) {
						return true;
					}

					if ( ! validator ) {
						validator = elements.form.data( 'validator' );
					}

					if ( ! validator ) {
						return true;
					}

					return validator.element( this.getValidateEl() );
				};

				/**
				 * Add HTML upon activation.
				 *
				 * Used for adding helper text.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 */
				LikertRow.prototype.addHTML = function() {

					if ( 1 === Object.keys( this.parentField.items.registered ).length ) {
						return;
					}

					var colspan = this.$el.find( 'td' ).length + 1;

					this.$el.after( '<tr class="wpforms-conversational-field-item-additional-html"><td colspan="' + colspan + '">' + wpforms_conversational_forms.html.likert_scale + '</td></tr>' );
				};

				/**
				 * Get element to be validated.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 *
				 * @returns {jQuery} Element to validate.
				 */
				LikertRow.prototype.getValidateEl = function() {

					return this.$el.find( 'input' );
				};

				return LikertRow;
			}() ),

			/**
			 * Google reCAPTCHA FieldItem child class.
			 *
			 * @since 1.0.0
			 *
			 * @type {object}
			 */
			RecaptchaHidden: ( function() {

				/**
				 * RecaptchaHidden FieldItem constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main FieldItem element.
				 * @param {string} id Unique FieldItem key.
				 * @param {string} type Type of FieldItem.
				 * @param {mainClasses.Field} parentField Parent Field object.
				 *
				 * @class
				 */
				function RecaptchaHidden( $el, id, type, parentField ) {

					mainClasses.FieldItem.call( this, $el, id, type, parentField );

					/**
					 * List of RecaptchaHidden FieldItem specific general events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.events.enable = {

						keyboardInput: {

							$el    : this.$el,
							handler: 'change',
							fn     : function() {

								if ( '1' !== this.$el.val() ) {
									return;
								}

								app.scroll.to( elements.footer ).then( function() {
									elements.footer.find( '.wpforms-submit' ).focus();
								} );
							},
						},
					};

					/**
					 * List of RecaptchaHidden FieldItem specific mobile general events to enable on activation.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					this.eventsMobile.enable = {

						keyboardInput: this.events.enable.keyboardInput,
					};
				}

				return RecaptchaHidden;
			}() ),
		},

		/**
		 * FieldItemsSet child classes.
		 *
		 * @since 1.0.0
		 */
		fieldItemsSet: {

			/**
			 * DateTime FieldItemsSet child class.
			 *
			 * @since 1.0.0
			 */
			DateTime: ( function() {

				/**
				 * DateTime FieldItemsSet constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {mainClasses.Field} field Field object.
				 *
				 * @class
				 */
				function DateTime( field ) {

					mainClasses.FieldItemsSet.call( this, field );
				}

				/**
				 * Identify element type.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Element to inspect.
				 *
				 * @override
				 *
				 * @returns {string} Element type.
				 */
				DateTime.prototype.identifyItemType = function( $el ) {

					if ( ! $el && this.current ) {
						$el = this.current.$el;
					}

					if ( ! $el ) {
						return '';
					}

					var type = $el.prop( 'type' );

					if ( $el.hasClass( 'wpforms-field-date-time-date' ) ) {
						type = 'date';
					}

					if ( $el.hasClass( 'wpforms-field-date-time-time' ) ) {
						type = 'time';
					}

					return type;
				};

				return DateTime;
			}() ),

			/**
			 * Signature FieldItemsSet child class.
			 *
			 * @since 1.0.0
			 */
			Signature: ( function() {

				/**
				 * Signature FieldItemsSet constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {mainClasses.Field} field Field object.
				 *
				 * @class
				 */
				function Signature( field ) {

					mainClasses.FieldItemsSet.call( this, field );
				}

				/**
				 * Find elements within FieldItemsSet.field.$el to be registered as FieldItems.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 *
				 * @returns {jQuery} Set of found elements.
				 */
				Signature.prototype.findElements = function() {

					// Signature has hidden text element that should not be included in a list of elements.
					return [];
				};

				return Signature;
			}() ),

			/**
			 * LikertScale Field Set child class.
			 *
			 * @since 1.0.0
			 */
			LikertScale: ( function() {

				/**
				 * LikertScale FieldItemsSet constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {mainClasses.Field} field Field object.
				 *
				 * @class
				 */
				function LikertScale( field ) {

					mainClasses.FieldItemsSet.call( this, field );
				}

				/**
				 * Find elements within FieldItemsSet.field.$el to be registered as FieldItems.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 *
				 * @returns {jQuery} Set of found elements.
				 */
				LikertScale.prototype.findElements = function() {

					return this.field.$el.find( 'tbody tr' );
				};

				/**
				 * Identify element type.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 *
				 * @returns {string} Element type.
				 */
				LikertScale.prototype.identifyItemType = function() {

					return 'likert_row';
				};

				return LikertScale;
			}() ),

			/**
			 * Google reCAPTCHA Field Set child class.
			 *
			 * @since 1.0.0
			 */
			Recaptcha: ( function() {

				/**
				 * Recaptcha FieldItemsSet constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {mainClasses.Field} field Field object.
				 *
				 * @class
				 */
				function Recaptcha( field ) {

					mainClasses.FieldItemsSet.call( this, field );
				}

				/**
				 * Find elements within FieldItemsSet.field.$el to be registered as FieldItems.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 *
				 * @returns {jQuery} Set of found elements.
				 */
				Recaptcha.prototype.findElements = function() {

					return this.field.$el.find( '.wpforms-recaptcha-hidden' );
				};

				/**
				 * Identify element type.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 *
				 * @returns {string} Element type.
				 */
				Recaptcha.prototype.identifyItemType = function() {

					return 'recaptcha-hidden';
				};

				return Recaptcha;
			}() ),
		},

		/**
		 * Field child classes.
		 *
		 * @since 1.0.0
		 */
		field: {

			/**
			 * Textarea Field child class.
			 *
			 * @since 1.0.0
			 */
			Textarea: ( function() {

				/**
				 * Textarea Field constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function Textarea( $el, id ) {

					mainClasses.Field.call( this, $el, id );

					/**
					 * List of global keyboard events to disable on Textarea FieldItem activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.disable = {

						up: app.globalEvents.keyboard.up,
						down: app.globalEvents.keyboard.down,
					};

					/**
					 * List of Textarea FieldItem specific keyboard events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.enable = {

						up: {

							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {

								if ( 38 !== e.keyCode ) {
									return;
								}

								if ( 0 === $( e.target ).prop( 'selectionStart' ) ) {
									app.scroll.prev();
								}
							},
						},

						down: {

							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {

								if ( 40 !== e.keyCode ) {
									return;
								}

								if ( $( e.target ).val().length === $( e.target ).prop( 'selectionStart' ) ) {
									app.scroll.next();
								}
							},
						},
					};

					/**
					 * List of Textarea Field specific general events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.events.enable = {

						userInput: {

							$el: this.$el.find( 'textarea' ),
							handler: 'input paste',
							fn: function() {

								var $el = this.$el.find( 'textarea' );

								var offset = $el.innerHeight() - $el.height();

								if ( $el.innerHeight < $el.get( 0 ).scrollHeight ) {

									// Grow the field if scroll height is smaller
									$el.height( $el.get( 0 ).scrollHeight - offset );
									return;
								}

								// Shrink the field and then re-set it to the scroll height in case it needs to shrink
								$el.height( 1 );
								$el.height( $el.get( 0 ).scrollHeight - offset );
							},
						},
					};

					/**
					 * List of Textarea Field specific mobile general events to enable on activation.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					this.eventsMobile.enable = {

						userInput: this.events.enable.userInput,
					};
				}

				/**
				 * Add HTML upon activation.
				 *
				 * Used for adding helper text.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 */
				Textarea.prototype.addHTML = function() {
					this.$el.append( '<div class="wpforms-conversational-field-additional-html">' + wpforms_conversational_forms.html.textarea + '</div>' );
				};

				return Textarea;
			}() ),

			/**
			 * Checkbox Field child class.
			 *
			 * @since 1.0.0
			 */
			Checkbox: ( function() {

				/**
				 * Checkbox Field constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function Checkbox( $el, id ) {

					mainClasses.Field.call( this, $el, id );

					/**
					 * List of global keyboard events to disable on Checkbox Field activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.disable = {

						enter: app.globalEvents.keyboard.enter,
					};

					/**
					 * List of Checkbox Field specific keyboard events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.enable = {

						alphabet: {
							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {

								// TODO: Get this.items.registered.$el set instead of searching the DOM.
								var checkboxes = this.$el.find( 'input[type="checkbox"]' );

								var index = e.keyCode - 65;
								if ( checkboxes[ index ] ) {
									$( checkboxes[ index ] ).trigger( 'click' );
								}
							},
						},

						enter: {

							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {
								if ( 13 === e.keyCode && ! e.shiftKey ) {
									e.preventDefault();
									if ( app.fields.callOnActive( 'isCurrentHighlighted' ) ) {
										app.fields.active.items.selectCurrent();
									}
								}
							},
						},

						shiftEnter: {
							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {

								if ( ! ( 13 === e.keyCode && e.shiftKey ) ) {
									return;
								}

								e.preventDefault();
								app.scroll.next();
							},
						},
					};
				}

				/**
				 * Add HTML upon activation.
				 *
				 * Used for adding helper text.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 */
				Checkbox.prototype.addHTML = function() {
					this.$el.append( '<div class="wpforms-conversational-field-additional-html">' + wpforms_conversational_forms.html.checkbox + '</div>' );
				};

				return Checkbox;
			}() ),

			/**
			 * Radio Field child class.
			 *
			 * @since 1.0.0
			 */
			Radio: ( function() {

				/**
				 * Radio Field constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function Radio( $el, id ) {

					mainClasses.Field.call( this, $el, id );

					/**
					 * List of Radio Field specific keyboard events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.enable = {

						alphabet: {
							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {

								// TODO: Get this.items.registered.$el set instead of searching the DOM.
								var radios = this.$el.find( 'input[type="radio"]' );

								var index = e.keyCode - 65;
								if ( radios[ index ] ) {
									$( radios[ index ] ).trigger( 'click' );
								}
							},
						},
					};
				}

				return Radio;
			}() ),

			/**
			 * FileUpload Field child class.
			 *
			 * @since 1.0.0
			 */
			FileUpload: ( function() {

				/**
				 * FileUpload Field constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function FileUpload( $el, id ) {

					mainClasses.Field.call( this, $el, id );

					/**
					 * List of FileUpload Field specific keyboard events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.enable = {

						shiftEnter: {
							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {

								if ( ! ( 13 === e.keyCode && e.shiftKey ) ) {
									return;
								}

								e.preventDefault();
								this.$el.find( 'input' ).trigger( 'click' );

								// Modern style upload.
								this.$el.find( '.wpforms-uploader' ).trigger( 'click' );
							},
						},
					};

					/**
					 * List of FileUpload Field specific general events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.events.enable = {

						changeFile: {
							$el: this.$el.find( 'input[type="file"]' ),
							handler: 'change',
							fn: function( e ) {

								var el = this.$el.find( 'input[type="file"]' ).get( 0 );

								if ( ! el.files || el.files.length !== 1 ) {
									return;
								}

								var fileName = e.target.value.split( '\\' ).pop();

								if ( ! fileName ) {
									return;
								}

								$( el ).nextAll( '.wpforms-field-file-upload-file-name' ).text( fileName );
								app.scroll.next();
							},
						},
					};

					/**
					 * List of FileUpload Field specific mobile general events to enable on activation.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					this.eventsMobile.enable = {

						changeFile: this.events.enable.changeFile,
					};
				}

				/**
				 * Add HTML upon activation.
				 *
				 * Used for adding helper text.
				 *
				 * @since 1.0.0
				 *
				 * @override
				 */
				FileUpload.prototype.addHTML = function() {
					this.$el.append( '<div class="wpforms-conversational-field-additional-html">' + wpforms_conversational_forms.html.file_upload + '</div>' );
				};

				return FileUpload;
			}() ),

			/**
			 * Rating Field child class.
			 *
			 * @since 1.0.0
			 */
			Rating: ( function() {

				/**
				 * Rating Field constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function Rating( $el, id ) {

					mainClasses.Field.call( this, $el, id );

					/**
					 * List of global keyboard events to disable on Rating Field activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.disable = {

						up: app.globalEvents.keyboard.up,
						down: app.globalEvents.keyboard.down,
					};

					/**
					 * List of Rating Field specific keyboard events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.enable = {

						numeric: {
							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {

								var items = this.items.registered;

								if ( ! e.keyCode ) {
									return;
								}

								var key = String.fromCharCode( e.keyCode );

								if ( isNaN( parseInt( key, 10 ) ) ) {
									return;
								}

								this.vars.keyBuffer = this.vars.keyBuffer || '';
								this.vars.keyBuffer += key;

								var itemToClick = helpers.object.getKeyByNumIndex(
									items,
									parseInt( this.vars.keyBuffer, 10 ) - 1
								);

								if ( ! itemToClick ) {
									itemToClick = helpers.object.getKeyByNumIndex(
										items,
										parseInt( key, 10 ) - 1
									);
								}

								if ( ! itemToClick ) {
									return;
								}

								if ( this.vars.timer ) {
									clearTimeout( this.vars.timer );
								}

								this.vars.timer = setTimeout( function() {
									delete this.vars.keyBuffer;
									delete this.vars.timer;
									itemToClick.$el.trigger( 'click' );
								}.bind( this ), 250 );
							},
						},

						up: {

							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {
								if ( 38 === e.keyCode ) {
									e.preventDefault();
									app.scroll.prev();
								}
							},
						},

						down: {

							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {
								if ( 40 === e.keyCode ) {
									e.preventDefault();
									app.scroll.next();
								}
							},
						},
					};
				}

				return Rating;
			}() ),

			/**
			 * NetPromoterScore Field child class.
			 *
			 * @since 1.0.0
			 */
			NetPromoterScore: ( function() {

				/**
				 * NetPromoterScore Field constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function NetPromoterScore( $el, id ) {

					mainClasses.Field.call( this, $el, id );

					/**
					 * List of global keyboard events to disable on NetPromoterScore Field activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.disable = {

						up: app.globalEvents.keyboard.up,
						down: app.globalEvents.keyboard.down,
					};

					/**
					 * List of NetPromoterScore Field specific keyboard events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.enable = {

						numeric: {
							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {

								var items = this.items.registered;

								if ( ! e.keyCode ) {
									return;
								}

								var key = String.fromCharCode( e.keyCode );

								if ( isNaN( parseInt( key, 10 ) ) ) {
									return;
								}

								this.vars.keyBuffer = this.vars.keyBuffer || '';
								this.vars.keyBuffer += key;

								var itemToClick = helpers.object.getKeyByNumIndex( items, this.vars.keyBuffer );

								if ( ! itemToClick ) {
									itemToClick = helpers.object.getKeyByNumIndex( items, key );
								}

								if ( ! itemToClick ) {
									return;
								}

								if ( this.vars.timer ) {
									clearTimeout( this.vars.timer );
								}

								this.vars.timer = setTimeout( function() {
									delete this.vars.keyBuffer;
									delete this.vars.timer;
									itemToClick.$el.trigger( 'click' );
								}.bind( this ), 250 );
							},
						},

						up: {

							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {
								if ( 38 === e.keyCode ) {
									e.preventDefault();
									app.scroll.prev();
								}
							},
						},

						down: {

							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {
								if ( 40 === e.keyCode ) {
									e.preventDefault();
									app.scroll.next();
								}
							},
						},
					};
				}

				return NetPromoterScore;
			}() ),

			/**
			 * LikertScale Field child class.
			 *
			 * @since 1.0.0
			 */
			LikertScale: ( function() {

				/**
				 * LikertScale Field constructor.
				 *
				 * @since 1.0.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function LikertScale( $el, id ) {

					mainClasses.Field.call( this, $el, id );

					/**
					 * List of global keyboard events to disable on LikertScale Field activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.disable = {

						enter: app.globalEvents.keyboard.enter,
						left: app.globalEvents.keyboard.left,
						right: app.globalEvents.keyboard.right,
					};

					/**
					 * List of LikertScale Field specific keyboard events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.keyboard.enable = {

						numeric: {
							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {

								if ( ! this.items.current ) {
									return;
								}

								// TODO: Get this.items.registered.$el set instead of searching the DOM.
								var radios = this.items.current.$el.find( 'input' );

								var index = e.keyCode - 49;
								if ( radios[ index ] ) {

									// jQuery Validate expects label to be clicked, not an input itself.
									$( radios[ index ] ).siblings( 'label' ).trigger( 'click' );
								}
							},
						},

						enter: {

							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {
								if ( 13 === e.keyCode && ! e.shiftKey ) {
									e.preventDefault();
									app.scroll.next();
								}
							},
						},

						left: {

							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {
								if ( 37 === e.keyCode ) {
									e.preventDefault();
								}
							},
						},

						right: {

							$el: $( window ),
							handler: 'keydown',
							fn: function( e ) {
								if ( 39 === e.keyCode ) {
									e.preventDefault();
								}
							},
						},
					};

					/**
					 * List of LikertScale Field specific general events to enable on activation.
					 *
					 * @since 1.0.0
					 *
					 * @type {object}
					 */
					this.events.enable = {

						activateFirstRow: {
							$el: this.$el,
							handler: 'wpformsConvFormsFieldActivationAfter',
							fn: function() {

								this.items.highlightNext();
							},
						},

						validation: {
							$el: this.$el,
							handler: 'wpformsConvFormsFieldValidation',
							fn: function( e, invalidIds ) {

								if ( ! invalidIds.length ) {
									return;
								}

								if ( ! ( invalidIds[0] in this.items.registered ) ) {
									return;
								}

								this.items.setCurrent( this.items.registered[ invalidIds[0] ] );

								this.items.current.fadeIn();
							},
						},
					};

					/**
					 * List of LikertScale Field specific mobile general events to enable on activation.
					 *
					 * @since 1.1.0
					 *
					 * @type {object}
					 */
					this.eventsMobile.enable = {

						validation: this.events.enable.validation,
					};
				}

				return LikertScale;
			}() ),

			/**
			 * Html Field child class.
			 *
			 * @since 1.2.0
			 */
			Html: ( function() {

				/**
				 * Html Field constructor.
				 *
				 * @since 1.2.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function Html( $el, id ) {

					mainClasses.Field.call( this, $el, id );
				}

				/**
				 * Add HTML upon activation.
				 *
				 * Used for adding helper text.
				 *
				 * @since 1.2.0
				 *
				 * @override
				 */
				Html.prototype.addHTML = function() {
					this.$el.append( '<div class="wpforms-conversational-field-additional-html">' + wpforms_conversational_forms.html.general.next_field + '</div>' );
				};

				return Html;
			}() ),

			/**
			 * Divider Field child class.
			 *
			 * @since 1.2.0
			 */
			Divider: ( function() {

				/**
				 * Divider Field constructor.
				 *
				 * @since 1.2.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function Divider( $el, id ) {

					mainClasses.Field.call( this, $el, id );
				}

				/**
				 * Add Divider upon activation.
				 *
				 * Used for adding helper text.
				 *
				 * @since 1.2.0
				 *
				 * @override
				 */
				Divider.prototype.addHTML = function() {
					this.$el.append( '<div class="wpforms-conversational-field-additional-html">' + wpforms_conversational_forms.html.general.next_field + '</div>' );
				};

				return Divider;
			}() ),

			/**
			 * PaymentSingle Field child class.
			 *
			 * @since 1.2.0
			 */
			PaymentSingle: ( function() {

				/**
				 * PaymentSingle Field constructor.
				 *
				 * @since 1.2.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function PaymentSingle( $el, id ) {

					mainClasses.Field.call( this, $el, id );
				}

				/**
				 * Add PaymentSingle upon activation.
				 *
				 * Used for adding helper text.
				 *
				 * @since 1.2.0
				 *
				 * @override
				 */
				PaymentSingle.prototype.addHTML = function() {
					this.$el.append( '<div class="wpforms-conversational-field-additional-html">' + wpforms_conversational_forms.html.general.next_field + '</div>' );
				};

				return PaymentSingle;
			}() ),

			/**
			 * PaymentTotal Field child class.
			 *
			 * @since 1.2.0
			 */
			PaymentTotal: ( function() {

				/**
				 * PaymentTotal Field constructor.
				 *
				 * @since 1.2.0
				 *
				 * @param {jQuery} $el Main Field element.
				 * @param {string} id Unique Field key.
				 *
				 * @borrows mainClasses.FieldItemsSet as items.
				 *
				 * @class
				 */
				function PaymentTotal( $el, id ) {

					mainClasses.Field.call( this, $el, id );
				}

				/**
				 * Add PaymentTotal upon activation.
				 *
				 * Used for adding helper text.
				 *
				 * @since 1.2.0
				 *
				 * @override
				 */
				PaymentTotal.prototype.addHTML = function() {
					this.$el.append( '<div class="wpforms-conversational-field-additional-html">' + wpforms_conversational_forms.html.general.next_field + '</div>' );
				};

				return PaymentTotal;
			}() ),
		},
	};

	/**
	 * Global events.
	 *
	 * @since 1.0.0
	 */
	globalEvents = {

		/**
		 * Global key mappings.
		 *
		 * @since 1.0.0
		 */
		keyboard: {

			enter: {

				$el: $( window ),
				handler: 'keydown',
				fn: function( e ) {

					if ( 13 === e.keyCode && ! e.shiftKey ) {

						e.preventDefault();

						if ( app.fields.isAtBaseline( elements.footer ) ) {
							elements.footer.find( '.wpforms-submit' ).trigger( 'click' );
							return;
						}

						if ( app.fields.callOnActive( 'isCurrentHighlighted' ) ) {
							app.fields.active.items.selectCurrent();
							return;
						}

						app.scroll.next();
					}
				},
			},


			space: {

				$el: $( window ),
				handler: 'keydown',
				fn: function( e ) {
					if ( 32 === e.keyCode ) {

						if ( app.fields.active.items.current.focusable ) {
							return;
						}

						e.preventDefault();

						if ( app.fields.callOnActive( 'isCurrentHighlighted' ) ) {
							app.fields.active.items.selectCurrent();
						}
					}
				},
			},

			tab: {

				$el: $( window ),
				handler: 'keydown',
				fn: function( e ) {
					if ( e.keyCode === 9 && ! e.shiftKey ) {
						e.preventDefault();
						try {
							app.fields.active.items.highlightNext().fail( app.scroll.next );
						} catch ( e ) {
							app.scroll.next();
						}
					}

					if ( e.keyCode === 9 && e.shiftKey ) {
						e.preventDefault();
						try {
							app.fields.active.items.highlightPrev().fail( app.scroll.prev );
						} catch ( e ) {
							app.scroll.prev();
						}
					}
				},
			},

			esc: {

				$el    : $( window ),
				handler: 'keydown',
				fn     : function( e ) {

					if ( 27 !== e.keyCode ) {
						return;
					}

					if ( app.fields.callOnActive( 'isCurrentHighlighted' ) ) {
						app.fields.active.items.setCurrent();
						app.fields.active.items.initCurrent();
					}
				},
			},

			up: {

				$el: $( window ),
				handler: 'keydown',
				fn: function( e ) {
					if ( 38 === e.keyCode ) {
						e.preventDefault();
						try {
							app.fields.active.items.highlightPrev().fail( app.scroll.prev );
						} catch ( e ) {
							app.scroll.prev();
						}
					}
				},
			},

			down: {

				$el: $( window ),
				handler: 'keydown',
				fn: function( e ) {
					if ( 40 === e.keyCode ) {
						e.preventDefault();
						try {
							app.fields.active.items.highlightNext().fail( app.scroll.next );
						} catch ( e ) {
							app.scroll.next();
						}
					}
				},
			},

			left: {

				$el: $( window ),
				handler: 'keydown',
				fn: function( e ) {
					if ( 37 === e.keyCode ) {

						if ( app.fields.active.items.current.focusable ) {
							return;
						}

						e.preventDefault();
						app.fields.callOnActive( 'highlightPrev' );
					}
				},
			},

			right: {

				$el: $( window ),
				handler: 'keydown',
				fn: function( e ) {
					if ( 39 === e.keyCode ) {

						if ( app.fields.active.items.current.focusable ) {
							return;
						}

						e.preventDefault();
						app.fields.callOnActive( 'highlightNext' );
					}
				},
			},
		},

		/**
		 * Global non-keyboard events.
		 *
		 * @since 1.0.0
		 */
		events: {

			clickActivateField: {

				$el: elements.fields,
				handler: 'mousedown',
				fn: function( e ) {

					var activeId = app.fields.active ? app.fields.active.id : null;

					var fieldId = $( this ).data( 'field-id' ) + '-' + $( this ).data( 'field-type' );

					if ( typeof fieldId === 'undefined' || ! ( fieldId in app.fields.registered ) ) {
						return;
					}

					if ( fieldId === activeId ) {
						return;
					}

					e.preventDefault();
					app.scroll.to( app.fields.registered[ fieldId ] );
				},
			},
		},
	};

	/**
	 * Global events for mobile.
	 *
	 * @since 1.1.0
	 */
	globalEventsMobile = {

		/**
		 * Global key mappings for mobile.
		 *
		 * @since 1.1.0
		 */
		keyboard: {

			enter: {

				$el: $( window ),
				handler: 'keydown',
				fn: function( e ) {

					if ( 13 === e.keyCode ) {

						e.preventDefault();

						try {
							app.fields.active.items.highlightNext().fail( app.scroll.next );
						} catch ( e ) {
							app.scroll.next();
						}
					}
				},
			},
		},

		/**
		 * Global non-keyboard events for mobile.
		 *
		 * @since 1.1.0
		 */
		events: {

			clickActivateField: globalEvents.events.clickActivateField,
		},
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.0.0
	 *
	 * @borrows mainClasses.FieldsSet as fields.
	 * @borrows scrollControl as scroll.
	 */
	app = {

		/**
		 * Main point of entry for Fields manipulations.
		 *
		 * @since 1.0.0
		 *
		 * @type {mainClasses.FieldsSet}
		 */
		fields: null,

		/**
		 * Controls both app and user page scrolling.
		 *
		 * @since 1.0.0
		 *
		 * @type {scrollControl}
		 */
		scroll: scrollControl,

		/**
		 * Global events.
		 *
		 * @since 1.1.0
		 *
		 * @type {globalEvents||globalEventsMobile}
		 */
		globalEvents: null,

		/**
		 * Mobile detection library instance.
		 *
		 * @since 1.1.0
		 *
		 * @type {MobileDetect}
		 */
		mobileDetect: null,

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			app.extendClasses();

			this.fields = new mainClasses.FieldsSet();

			if ( typeof MobileDetect !== 'undefined' ) {
				app.mobileDetect = new MobileDetect( window.navigator.userAgent );
			}

			this.globalEvents = app.isMobileDevice() ? globalEventsMobile : globalEvents;

			$( document ).ready( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready: function() {

			app.hidePreloader();

			if ( ! app.readyToStart() ) {
				app.runNotReadyActions();
				return;
			}

			app.setup();
			app.events();
		},

		/**
		 * Check if client device is mobile.
		 *
		 * @since 1.1.0
		 *
		 * @returns {boolean} Client device is mobile.
		 */
		isMobileDevice: function() {

			if ( ! app.mobileDetect ) {
				return false;
			}

			return !! app.mobileDetect.mobile();
		},

		/**
		 * Extend classes.
		 *
		 * @since 1.0.0
		 */
		extendClasses: function() {

			$.each( childClasses, function( typeName, type ) {
				$.each( type, function( className, subClass ) {
					helpers.class.extend(
						subClass,
						mainClasses[ helpers.string.toCapitalizedCamelCase( typeName ) ]
					);
				} );
			} );
		},

		/**
		 * Hide form preloader.
		 *
		 * @since 1.0.0
		 */
		hidePreloader: function() {
			$( 'html' ).removeClass( 'wpforms-conversational-form-loading' );
		},

		/**
		 * Check if form is ready to start.
		 *
		 * @since 1.0.0
		 *
		 * @returns {boolean} Form is ready to start.
		 */
		readyToStart: function() {
			return ! $( '.wpforms-confirmation-container' ).length && ! $( '.wpforms-confirmation-container-full' ).length;
		},

		/**
		 * Run actions if form is not ready to start.
		 *
		 * @since 1.0.0
		 */
		runNotReadyActions: function() {

			$( '.wpforms-conversational-form-footer-progress-status-proportion' ).hide();
			$( '.wpforms-conversational-form-footer-progress-status-proportion-completed' ).show();

			$( '.wpforms-conversational-form-footer-switch-step' ).hide();
		},

		/**
		 * App setup.
		 *
		 * @since 1.0.0
		 */
		setup: function() {

			app.addRecaptchaToRegisteredFields();

			app.fields.updateRegistered();

			app.loadValidation();
			app.mapAllGlobalEvents();

			app.runInitialActions();

			app.fields.updateActive();
		},

		/**
		 * App events.
		 *
		 * @since 1.0.0
		 */
		events: function() {

			$( window ).on( 'scroll', app.scroll.passive );

			$( '.wpforms-conversational-btn-start' ).click( app.scroll.next );

			$( '.wpforms-conversational-form-footer-switch-step-up' ).click( app.footerStepUpBtnAction );

			$( '.wpforms-conversational-form-footer-switch-step-down' ).click( app.footerStepDownBtnAction );

			$( document ).on( 'wpformsProcessConditionalsField', app.updateProgressBar );
		},

		/**
		 * Add Google reCAPTCHA (if enabled) to the form elements.
		 *
		 * @since 1.0.0
		 */
		addRecaptchaToRegisteredFields: function() {

			if ( ! elements.recaptchaContainer.length ) {
				return;
			}

			var $recaptchaEl = elements.recaptchaContainer.find( '.g-recaptcha' );

			if ( ! $recaptchaEl.length ) {
				return;
			}

			if ( 'invisible' === $recaptchaEl.data( 'size' ) ) {
				return;
			}

			elements.recaptchaContainer
				.attr( 'data-field-type', 'recaptcha' )
				.attr( 'data-field-id', 'g' );

			elements.fields = elements.fields.add( elements.recaptchaContainer );
		},

		/**
		 * Load jQuery Validate custom settings.
		 *
		 * @since 1.0.0
		 */
		loadValidation: function() {

			if ( typeof $.fn.validate === 'undefined' ) {
				return;
			}

			setTimeout( function() {

				var validator = elements.form.data( 'validator' );

				if ( ! validator ) {
					return;
				}

				$.validator.addMethod( 'wpforms-conversational-forms-date', function( value, element ) {
					return this.optional( element ) || /^\d{1,2}\/\d{2}\/\d{4}$/.test( value );
				}, $.validator.messages.date );

				validator.settings.focusInvalid = false;

				// TODO: Dropdown object needs a method getInput() instead of '.wpforms-conversational-form-dropdown-input input'.
				validator.settings.ignore = ':hidden, .wpforms-conversational-form-dropdown-input input';
				validator.settings.invalidHandler = function( event, validator ) {

					var errors = validator.numberOfInvalids();
					if ( ! errors || ! validator.errorList.length ) {
						return;
					}

					var id   = $( validator.errorList[ 0 ].element ).closest( '.wpforms-field' ).data( 'field-id' );
					var type = $( validator.errorList[ 0 ].element ).closest( '.wpforms-field' ).data( 'field-type' );

					// TODO: mainClasses.FieldsSet needs getFieldIdFromElement( $el ) method.
					if ( ( id + '-' + type ) in app.fields.registered ) {
						app.scroll.to( app.fields.registered[ id + '-' + type ] );
					}
				};

				elements.form.on( 'invalid-form.validate', validator.settings.invalidHandler );

			}, 0 );
		},

		/**
		 * Map all (both general and keyboard) global events from globalEvents.
		 *
		 * @since 1.0.0
		 */
		mapAllGlobalEvents: function() {

			$.each( app.globalEvents.events, function( key ) {
				app.globalEvents.events[ key ].$el
					.on(
						app.globalEvents.events[ key ].handler,
						app.globalEvents.events[ key ].fn
					);
			} );

			$.each( app.globalEvents.keyboard, function( key ) {

				app.globalEvents.keyboard[ key ].$el
					.on(
						app.globalEvents.keyboard[ key ].handler,
						app.globalEvents.keyboard[ key ].fn
					);
			} );
		},

		/**
		 * Unmap all (both general and keyboard) global events from globalEvents.
		 *
		 * @since 1.0.0
		 */
		unmapAllGlobalEvents: function() {

			$.each( app.globalEvents.events, function( key ) {
				app.globalEvents.events[ key ].$el
					.off(
						app.globalEvents.events[ key ].handler,
						app.globalEvents.events[ key ].fn
					);
			} );

			$.each( app.globalEvents.keyboard, function( key ) {
				app.globalEvents.keyboard[ key ].$el
					.off(
						app.globalEvents.keyboard[ key ].handler,
						app.globalEvents.keyboard[ key ].fn
					);
			} );
		},

		/**
		 * Run initial actions after form setup.
		 *
		 * @since 1.0.0
		 */
		runInitialActions: function() {

			if ( app.scroll.isTop() && ! elements.phpErrorContainer.length ) {
				elements.page.addClass( 'wpforms-conversational-form-start' );
			}

			if ( elements.phpErrorContainer.length ) {
				app.scroll.to( elements.phpErrorContainer );
			}
		},

		/**
		 * Update footer progress bar.
		 *
		 * Detects a type of the bar.
		 *
		 * @since 1.0.0
		 */
		updateProgressBar: function() {

			if ( elements.progress.totalCount.length ) {
				app.updateProportionProgressBar();
			} else {
				app.updatePercentageProgressBar();
			}
		},

		/**
		 * Update footer progress bar (proportion).
		 *
		 * @since 1.0.0
		 */
		updateProportionProgressBar: function() {

			var visibleFields  = app.fields.getVisible(),
				completedOf    = Object.keys( visibleFields ).length,
				completedCount = app.fields.isAtBaseline( elements.footer ) ? completedOf : app.fields.getCompletedCount( visibleFields ),
				progress       = app.fields.getCompletedPercent( visibleFields, completedCount );

			elements.progress.bar.width( progress + '%' );
			elements.progress.completed.text( completedCount );
			elements.progress.totalCount.text( completedOf );

		},

		/**
		 * Update footer progress bar (percentage).
		 *
		 * @since 1.0.0
		 */
		updatePercentageProgressBar: function() {

			var progress = app.fields.getCompletedPercent();

			elements.progress.bar.width( progress + '%' );
			elements.progress.completed.text( progress );
		},

		/**
		 * Callback for footer "Step Up" button.
		 *
		 * @since 1.1.0
		 */
		footerStepUpBtnAction: function() {

			var elementType = app.fields.callOnActive( 'identifyItemType' );

			// Footer "Up" button skips to previous
			if ( [ 'checkbox', 'radio' ].indexOf( elementType ) !== -1 ) {
				app.scroll.prev();
				return;
			}

			try {
				app.fields.active.items.highlightPrev().fail( app.scroll.prev );
			} catch ( e ) {
				app.scroll.prev();
			}
		},

		/**
		 * Callback for footer "Step Down" button.
		 *
		 * @since 1.1.0
		 */
		footerStepDownBtnAction: function() {

			var elementType = app.fields.callOnActive( 'identifyItemType' );

			if ( [ 'checkbox', 'radio' ].indexOf( elementType ) !== -1 ) {
				app.scroll.next();
				return;
			}

			try {
				app.fields.active.items.highlightNext().fail( app.scroll.next );
			} catch ( e ) {
				app.scroll.next();
			}
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsConversationalForms.init();
