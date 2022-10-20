/* global wpforms_form_abandonment, MobileDetect */

'use strict';

/**
 * WPForms Form Abandonment function.
 *
 * @since 1.2.1
 * @package WPFormsFormAbandonment
 *
 * @namespace
 */
var WPFormsFormAbandonment = window.WPFormsFormAbandonment || ( function( document, window, $ ) {

	var data = {},
		json = false,
		sent = false,
		currentFormID;

	var app = {

		/**
		 * Mobile detection library instance.
		 *
		 * @since 1.2.1
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

			if ( typeof MobileDetect !== 'undefined' ) {
				app.mobileDetect = new MobileDetect( window.navigator.userAgent );
			}

			// Form interactions.
			$( document ).on( 'input', '.wpforms-form-abandonment :input', app.prepData );
			$( document ).on( 'change', '.wpforms-form-abandonment input[type=radio]', app.prepData );
			$( document ).on( 'change', '.wpforms-form-abandonment input[type=checkbox]', app.prepData );
			$( document ).on( 'change', '.wpforms-form-abandonment .wpforms-timepicker', app.prepData );

			// Abandonment events.
			$( document ).on( 'mouseleave', this.abandonMouse );

			if ( app.isMobileDevice() ) {

				// Hack for iOS devices. Sometimes, click event does not work(!).
				if ( app.isIOSDevice() ) {
					$( 'a' ).css( 'cursor', 'pointer' );
				}

				// Trick for older mobile browsers.
				var touchEvent = 'ontouchstart' in window ? 'touchstart' : 'click';
				$( document ).on( touchEvent, this.abandonClick );
			} else {
				$( document ).on( 'mousedown', this.abandonClick );
			}

			$( window ).on( 'beforeunload', this.abandonBeforeUnload );

			$( '.wpforms-form' ).on( 'wpformsAjaxSubmitSuccess', app.ajaxSubmitSuccess );
		},

		/**
		 * Check if client device is mobile.
		 *
		 * @since 1.2.1
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
		 * Check if client device is iOS device.
		 *
		 * @since 1.2.1
		 *
		 * @returns {boolean} Client device is mobile.
		 */
		isIOSDevice: function() {

			if ( ! app.mobileDetect ) {
				return false;
			}

			return app.mobileDetect.os() === 'iOS';
		},

		/**
		 * As the field inputs change, update the data on the fly.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event Event obj.
		 */
		prepData: function( event ) {

			var $form  = $( event.target ).closest( '.wpforms-form' );

			currentFormID         = $form.data( 'formid' );
			data[ currentFormID ] = $form.serializeArray();
			json                  = JSON.stringify( data );

			app.debug( 'Preping data' );
		},

		/**
		 * Send the data.
		 *
		 * @since 1.0.0
		 */
		sendData: function() {

			// Don't do anything if the user has not starting filling out a form
			// or if we have already recently sent one.
			if ( ! json || sent ) {
				return;
			}

			// Skip if no data prepared for the current form.
			// For instance: after submitting AJAX form.
			if ( ! currentFormID || typeof data[ currentFormID ] === 'undefined' ) {
				return;
			}

			// This is used to rate limit so that we never post more than once
			// every 10 seconds.
			sent = true;
			setTimeout( function() {
				sent = false;
			}, 10000 );

			app.debug( 'Sending' );

			// Send the form(s) data via ajax.
			$.post( wpforms_form_abandonment.ajaxurl, {
				action: 'wpforms_form_abandonment',
				forms: json,
			} );

			data = {};
			json = false;
		},

		/**
		 * Abandoned via mouseleave.
		 *
		 * This triggers when the user's mouse leaves the page.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event Event obj.
		 */
		abandonMouse: function( event ) {

			// Set a few reasonable boundaries
			if ( event.offsetX < -1 || event.clientY > 20 ) {
				return;
			}

			app.debug( 'Mouse abandoned' );

			app.sendData();
		},

		/**
		 * Abaondoned via click.
		 *
		 * This triggers when the user clicks on the page.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event Event obj.
		 */
		abandonClick: function( event ) {

			var el = event.srcElement || event.target;

			// Loop up the DOM tree through parent elements if clicked element is not a link (eg: an image inside a link).
			while ( el && ( typeof el.tagName === 'undefined' || el.tagName.toLowerCase() !== 'a' || ! el.href ) ) {
				el = el.parentNode;
			}

			if ( ! el || ! el.href ) {
				return;
			}

			/*
			 * If a link with valid href has been clicked.
			 */

			app.debug( 'Click abandoned' );

			var link = el.href,
				type = 'internal';

			// Determine click event type.
			if ( el.protocol === 'mailto' ) { // Email.
				type = 'mailto';
			} else if ( link.indexOf( wpforms_form_abandonment.home_url ) === -1 ) { // Outbound.
				type = 'external';
			}

			// Trigger form abandonment with internal and external links.
			if ( [ 'external', 'internal' ].indexOf( type ) < 0 || link.match( /^javascript\:/i ) ) {
				return;
			}

			// Is actual target set and not _(self|parent|top)?
			var target = ( el.target && ! el.target.match( /^_(self|parent|top)$/i ) ) ? el.target : false;

			// Assume a target if Ctrl|shift|meta-click.
			if ( event.ctrlKey || event.shiftKey || event.metaKey || event.which === 2 ) {
				target = '_blank';
			}

			if ( ! target && ! app.isIOSDevice() ) {
				return;
			}

			// If target opens a new window or this is iOS device then trigger abandoned entry.
			app.sendData();
		},

		/**
		 * Window before unload.
		 *
		 * This triggers when the window unload.
		 *
		 * @since 1.2.1
		 *
		 * @param {object} event Event obj.
		 */
		abandonBeforeUnload: function( event ) {

			app.debug( 'Before unload abandoned' );

			app.sendData();
		},

		/**
		 * Ajax submit success event.
		 *
		 * This triggers when the Ajax form submitted successfully.
		 *
		 * @since 1.4.4
		 */
		ajaxSubmitSuccess: function() {

			app.debug( 'Ajax submit success event' );

			var formID = $( this ).data( 'formid' ),
				undef;

			// We should clear abandonment data for the current form when ajax form submitted successfully.
			// This is needed to avoid creating a new `abandoned` entry which duplicates the already submitted entry.
			delete data[ formID ];
			currentFormID = undef;

			json = JSON.stringify( data );
		},

		/**
		 * Optional debug messages.
		 *
		 * @since 1.0.2
		 *
		 * @param {string} msg Debug message.
		 */
		debug: function( msg ) {

			if ( window.location.hash && '#wpformsfadebug' === window.location.hash ) {
				console.log( 'WPForms FA: ' + msg );
			}
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

WPFormsFormAbandonment.init();
