/* globals wpforms, wpforms_settings, wpforms_form_locker */

'use strict';

/**
 * WPForms Form Locker function.
 *
 * @since 1.0.0
 */
var WPFormsFormLocker = window.WPFormsFormLocker || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 1.0.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			$( document ).ready( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready: function() {

			app.validation();
			app.initNameGroupsValidation();
		},

		/**
		 * Register custom validation.
		 *
		 * @since 1.0.0
		 */
		validation: function() {

			// Only load if jQuery validation library exists
			if ( typeof $.fn.validate === 'undefined' ) {
				return;
			}

			$.validator.addMethod( 'unique', function( value, element, param, method ) { // eslint-disable-line

				// This code is copied from JQuery Validate 'remote' method with several changes:
				// - 'data' variable is not empty;
				// - 'url' and 'type' parameters are added to $.ajax() call.
				// - Added support for the complex `Name` field. Passing combined value instead of the single input.
				if ( this.optional( element ) ) {
					return 'dependency-mismatch';
				}

				method = typeof method === 'string' && method || 'unique';

				var $el = $( element ),
					$field = $el.closest( '.wpforms-field' ),
					previous = this.previousValue( element, method ),
					validator,
					data,
					optionDataString,
					name;

				data = {
					'action': 'wpforms_form_locker_unique_answer',
					'form_id': $el.closest( '.wpforms-form' ).data( 'formid' ),
					'field_id': $field.data( 'field-id' ),
				};

				name = element.name;

				// In case of the `Name` field we need to get combined value.
				if ( $field.hasClass( 'wpforms-field-name' ) ) {
					value = app.getCombinedValue( $field );
					name = 'wpforms[fields][' + data['field_id'] + ']';
				}

				data[ name ] = value;

				if ( ! this.settings.messages[ element.name ] ) {
					this.settings.messages[ element.name ] = {};
				}
				previous.originalMessage = previous.originalMessage || this.settings.messages[ element.name ][ method ];
				this.settings.messages[ element.name ][ method ] = previous.message;

				param = typeof param === 'string' && { url: param } || param;
				optionDataString = $.param( $.extend( { data: value }, param.data ) );
				if ( previous.old === optionDataString ) {

					return previous.valid;
				}

				previous.old = optionDataString;
				validator = this;
				this.startRequest( element );

				$.ajax( $.extend( true, {
					url: wpforms_form_locker.ajaxurl,
					type: 'post',
					mode: 'abort',
					port: 'validate' + element.name,
					dataType: 'json',
					data: data,
					context: validator.currentForm,
					success: function( response ) {

						var valid = response === true || response === 'true',
							errors, message, submitted;

						// To get the subfield name prefix we should remove subfield name suffix (like [first]).
						var	subfieldPrefix = element.name.replace( /(\[[a-z]*\])$/g, '' );

						validator.settings.messages[ element.name ][ method ] = previous.originalMessage;
						if ( valid ) {
							submitted = validator.formSubmitted;
							validator.resetInternals();
							validator.toHide = validator.errorsFor( element );
							validator.formSubmitted = submitted;
							validator = app.updateValidatorLists( validator, subfieldPrefix, $field, valid );
							validator.showErrors();
						} else {
							errors = {};
							message = response || validator.defaultMessage( element, { method: method, parameters: value } );
							errors[ element.name ] = previous.message = message;
							validator = app.updateValidatorLists( validator, subfieldPrefix, $field, valid );
							validator.showErrors( errors );
						}
						previous.valid = valid;
						validator.stopRequest( element, valid );
					},
				}, param ) );
				return 'pending';
			}, wpforms_settings.val_unique );
		},

		/**
		 * Init validator groups for all unique Name fields.
		 *
		 * @since {VERSION}
		 */
		initNameGroupsValidation: function() {

			$( '.wpforms-validate' ).each( function() {

				var $form = $( this ),
					validator = $form.data( 'validator' );

				$.extend( validator.groups, app.getNameGroups( $form ) );
			} );
		},

		/**
		 * Get field combined value.
		 *
		 * @since {VERSION}
		 *
		 * @param {jQuery} $field WPForms field jQuery object.
		 *
		 * @returns {string} Field combined value.
		 */
		getCombinedValue: function( $field ) {

			var values = [];

			$field.find( 'input[data-rule-unique]' ).each( function() {

				var val = $( this ).val();

				if ( ! wpforms.empty( val ) ) {
					values.push( val );
				}
			} );

			return values.join( ' ' );
		},

		/**
		 * Generate validator groups data.
		 *
		 * @since {VERSION}
		 *
		 * @param {jQuery} $form Form jQuery object.
		 *
		 * @returns {object} Array like object.
		 */
		getNameGroups: function( $form ) {

			var result = {};

			$form.find( '.wpforms-field.wpforms-field-name.wpforms-field-unique' ).each( function( f ) {

				var $field = $( this ),
					groupName = $field.attr( 'id' ).replace( '-container', '' ) + '-field-group';

				$.each( [ 'first', 'middle', 'last' ], function( i, subfield ) {

					var $subfield = $field.find( '.wpforms-field-name-' + subfield ),
						name = $subfield.attr( 'name' ) || false;

					if ( name ) {
						result[ name ] = groupName;
					}
				} );
			} );
			return result;
		},

		/**
		 * Update validator lists.
		 *
		 * @since {VERSION}
		 *
		 * @param {object} validator      jQuery validator object.
		 * @param {string} subfieldPrefix Subfield name prefix.
		 * @param {jQuery} $field         WPForms field jQuery object.
		 * @param {bool}   valid          Field validation state.
		 *
		 * @returns {object} Modified jQuery validator object.
		 */
		updateValidatorLists: function( validator, subfieldPrefix, $field, valid ) {

			$.each( validator.invalid, function( key ) {

				if ( key.indexOf( subfieldPrefix ) < 0 ) {
					return;
				}
				validator.invalid[ key ] = ! valid;
				var $input = $field.find( 'input[name="' + key + '"]' );
				if ( valid ) {
					validator.successList.push( $input[0] );
				}
			} );

			return validator;
		},
	};

	// Provide access to public functions/properties.
	return app;

} )( document, window, jQuery );

// Initialize.
WPFormsFormLocker.init();
