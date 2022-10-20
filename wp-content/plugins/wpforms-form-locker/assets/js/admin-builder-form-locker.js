/* globals wpforms_admin_builder_form_locker */

'use strict';

/**
 * WPForms Builder Form Locker function.
 *
 * @since 1.0.0
 */
var WPFormsBuilderFormLocker = window.WPFormsBuilderFormLocker || ( function( document, window, $ ) {

	/**
	 * Detepair DefaultTimeDelta parameter (minutes).
	 *
	 * @since 1.2.2
	 *
	 * @type {number}
	 */
	var datePairDefaultTimeDelta = 30;

	/**
	 * Elements.
	 *
	 * @since 1.2.2
	 *
	 * @type {object}
	 */
	var el = {
		scheduling: {
			start: {
				$dpicker: $( '#wpforms-panel-field-settings-form_locker_schedule_start_date' ),
				$tpicker: $( '#wpforms-panel-field-settings-form_locker_schedule_start_time' ),
			},
			end: {
				$dpicker: $( '#wpforms-panel-field-settings-form_locker_schedule_end_date' ),
				$tpicker: $( '#wpforms-panel-field-settings-form_locker_schedule_end_time' ),
			},
		},
	};

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

			app.events();
			app.conditionals();
			app.dateTimePicker();
			app.scheduleDatetimeEvents();
		},

		/**
		 * Register JS events.
		 *
		 * @since 1.0.0
		 */
		events: function() {

			$( '.wpforms-panel-field-datetime .wpforms-clear-datetime-field' ).click( function() {
				var $input = $( this ).siblings( 'input' );
				if ( $input.prop( '_flatpickr' ) ) {
					$input.prop( '_flatpickr' ).clear();
				} else {
					$input.val( '' );
				}
				$( this ).hide();
			} );
		},

		/**
		 * Load conditionals.
		 *
		 * @since 1.0.0
		 */
		conditionals: function() {

			if ( typeof $.fn.conditions === 'undefined' ) {
				return;
			}

			var elements = [
				{
					id: '#wpforms-panel-field-settings-form_locker_password_enable',
					hides: '#wpforms-panel-field-settings-form_locker_password-wrap,#wpforms-panel-field-settings-form_locker_password_message-wrap',
				},
				{
					id: '#wpforms-panel-field-settings-form_locker_schedule_enable',
					hides: '#wpforms-form-locker-schedule-datetime-block,#wpforms-panel-field-settings-form_locker_schedule_message-wrap',
				},
				{
					id: '#wpforms-panel-field-settings-form_locker_entry_limit_enable',
					hides: '#wpforms-panel-field-settings-form_locker_entry_limit-wrap,#wpforms-panel-field-settings-form_locker_entry_limit_message-wrap',
				},
				{
					id: '#wpforms-panel-field-settings-form_locker_user_enable',
					hides: '#wpforms-panel-field-settings-form_locker_user_message-wrap',
				},
			];

			$.each( elements, function( index, element ) {
				$( element.id ).conditions( {
					conditions: {
						element : element.id,
						type    : 'checked',
						operator: 'is',
					},
					actions   : {
						if  : {
							element: element.hides,
							action : 'show',
						},
						else: {
							element: element.hides,
							action : 'hide',
						},
					},
					effect    : 'appear',
				} );
			} );
		},

		/**
		 * Load date/time pickers.
		 *
		 * @since 1.0.0
		 */
		dateTimePicker: function() {

			app.datePicker();
			app.timePicker();
			app.datePair();
			app.dateTimeClearBtnsInit();
		},

		/**
		 * Load date picker.
		 *
		 * @since 1.0.0
		 */
		datePicker: function() {

			if ( 'undefined' === typeof $.fn.flatpickr ) {
				return;
			}

			$( '#wpforms-panel-field-settings-form_locker_schedule_start_date' ).flatpickr( app.datePickerGetArgs( 'start' ) );
			$( '#wpforms-panel-field-settings-form_locker_schedule_end_date' ).flatpickr( app.datePickerGetArgs( 'end' ) );
		},

		/**
		 * Load time picker.
		 *
		 * @since 1.0.0
		 */
		timePicker: function() {

			if ( 'undefined' === typeof $.fn.timepicker ) {
				return;
			}

			var args = {
					appendTo: $( '#wpforms-builder' ),
					disableTextInput: true,
					timeFormat: wpforms_admin_builder_form_locker.time_format,
				},
				$startTime = $( '#wpforms-panel-field-settings-form_locker_schedule_start_time' ),
				$endTime   = $( '#wpforms-panel-field-settings-form_locker_schedule_end_time' );

			$startTime.timepicker( args ).on( 'selectTime', app.dateTimeClearBtnsRefresh );
			$endTime.timepicker( args ).on( 'selectTime', app.dateTimeClearBtnsRefresh );
		},

		/**
		 * Load date pair.
		 *
		 * @since 1.0.0
		 */
		datePair: function() {

			var args = {
				anchor: null,
				defaultDateDelta: 0,
				defaultTimeDelta: datePairDefaultTimeDelta * 60 * 1000,
				dateClass: 'wpforms-datepair-date',
				timeClass: 'wpforms-datepair-time',
				startClass: 'wpforms-datepair-start',
				endClass: 'wpforms-datepair-end',
				parseDate: function( input ) {
					return $( input ).prop( '_flatpickr' ).selectedDates[0];
				},
				updateDate: function( input, dateObj ) {

					var $input = $( input );

					$input.prop( '_flatpickr' ).setDate( dateObj );
					app.dateTimeClearBtnsRefresh( $input );
					$input.trigger( 'change' );
				},
				updateTime: function( input, dateObj ) {

					var $input = $( input );

					$input.timepicker( 'setTime', dateObj );
					app.dateTimeClearBtnsRefresh( $input );
					$input.trigger( 'change' );
				},
			};

			$( '#wpforms-form-locker-schedule-datetime-block' ).datepair( args );
		},

		/**
		 * Init delete/time field clear buttons.
		 *
		 * @since 1.2.0
		 */
		dateTimeClearBtnsInit: function() {

			$( '#wpforms-form-locker-schedule-datetime-block .wpforms-clear-datetime-field' )
				.each( function() {
					var $t = $( this );
					if ( '' === $t.siblings( '[id^="wpforms-panel-field-settings-form_locker_schedule_"]' ).val() ) {
						$t.hide();
					}
				} );
		},

		/**
		 * Refresh a clear button for an element.
		 *
		 * @since 1.2.2
		 *
		 * @param {jQuery|Event} $el Element to refresh a clear button for.
		 */
		dateTimeClearBtnsRefresh: function( $el ) {

			// Extract the element if an event was passed.
			// Useful when used as a callback.
			if ( $el.target ) {
				$el = $( $el.target );
			}

			if ( ! $el ) {
				return;
			}

			if ( '' !== $el.val() ) {
				$el.nextAll( 'button.wpforms-clear-datetime-field' ).show();
			}
		},

		/**
		 * Get arguments for date picker start/end date fields.
		 *
		 * @since 1.2.2
		 *
		 * @param {string} field Field type (e.g 'start' or 'end').
		 *
		 * @returns {{altFormat: string, dateFormat: string, altInput: boolean, onChange: Function}} Date picker arguments.
		 */
		datePickerGetArgs: function( field ) {

			field = field === 'start' ? 'start' : 'end';

			var $tpicker = el.scheduling[field].$tpicker,
				$dpicker = el.scheduling[field].$dpicker,
				args     = {
					altInput  : true,
					altFormat : wpforms_admin_builder_form_locker.date_format,
					dateFormat: 'Y-m-d',
				};

			args.onChange = function( date ) {

				var second = {
					field : field === 'start' ? 'end' : 'start',
				};

				second.$dpicker = el.scheduling[second.field].$dpicker;
				second.$tpicker = el.scheduling[second.field].$tpicker;

				if (
					$tpicker.val() === '' &&
					second.$tpicker.val() === ''
				) {
					$tpicker.timepicker( 'setTime', new Date( date ) );
				}

				app.dateTimeClearBtnsRefresh( $tpicker );
				app.dateTimeClearBtnsRefresh( $dpicker );
			};

			return args;
		},

		/**
		 * Schedule datetime events.
		 *
		 * @since 1.2.2
		 */
		scheduleDatetimeEvents: function() {

			// This is needed for handling wrong datetime range situations.
			$( '#wpforms-form-locker-schedule-datetime-block' ).on( 'change', 'input', function( e ) {

				var start = {
						date: el.scheduling.start.$dpicker.val(),
						time: el.scheduling.start.$tpicker.val(),
					},
					end = {
						date: el.scheduling.end.$dpicker.val(),
						time: el.scheduling.end.$tpicker.val(),
					};

				if ( start.date === '' || end.date === '' ) {
					return;
				}

				var sdate = ( start.date + start.time ).replace( /\D/g, '' ),
					edate = ( end.date + end.time ).replace( /\D/g, '' );

				if ( edate > sdate ) {
					return;
				}

				var edateObj = new Date( start.date + ' ' + start.time );
				edateObj.setMinutes( edateObj.getMinutes() + datePairDefaultTimeDelta );
				el.scheduling.end.$tpicker.timepicker( 'setTime', edateObj );
				el.scheduling.end.$dpicker.prop( '_flatpickr' ).setDate( edateObj );

				app.dateTimeClearBtnsRefresh( el.scheduling.end.$tpicker );
				app.dateTimeClearBtnsRefresh( el.scheduling.end.$dpicker );
			} );
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsBuilderFormLocker.init();
