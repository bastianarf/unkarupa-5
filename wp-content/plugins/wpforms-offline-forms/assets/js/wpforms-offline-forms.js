/* globals wpforms_offline_forms, store */

'use strict';

var WPFormsOfflineForms = window.WPFormsOfflineForms || ( function( document, window, $ ) {

	var app = {

		isOffline: false,
		forms: [],
		formsData: {},

		/**
		 * Manage data in LocalStorage. With namespaces, expiration and proper nesting.
		 *
		 * @since 1.0.0
		 */
		storage: {

			local: store.namespace( 'wpforms' ).namespace( 'offline' ),

			// Number of seconds each storage record will be valid for.
			expire: 60 * 60 * 24,

			/**
			 * Save the values into the storage under form namespace.
			 *
			 * @param {number} formId  Form ID.
			 * @param {Array} formData Form data.
			 */
			set: function( formId, formData ) {

				var form = this.local.namespace( parseInt( formId, 10 ) );

				// Doing +1 to always have a unique key for entry and
				// to NOT iterate/delete old/save new data, as this is an expensive & DOM-blocking operation.
				form.set( form.size() + 1, formData, this.expire );
			},

			/**
			 * Get and remove from storage the single the oldest offline form entry.
			 *
			 * @param {number} formId Form ID.
			 *
			 * @returns {object} Single entry having {entry_id:1,entry:"{}"} structure.
			 */
			get: function( formId ) {

				formId = parseInt( formId, 10 );
				var entries = this.getAll( formId ),
					first = {};

				// Now we need to take only the first entry and remove it from the saved.
				if ( entries.length > 0 ) {
					first = entries.shift();
				}

				// Remove the requested value from storage.
				this.local.namespace( formId )
					.remove( first.entry_id );

				return first;
			},

			/**
			 * Get all the offline form entries.
			 *
			 * @param {number} formId Form ID.
			 *
			 * @returns {Array} All the entries, each entry having {entry_id:1,entry:"{}"} structure.
			 */
			getAll: function( formId ) {

				var data = this.local.namespace( parseInt( formId, 10 ) ).getAll(),
					entries = [],
					entryId;

				// Remap data object to an array to have handy Array.prototype.* functions available.
				for ( entryId in data ) {
					// eslint-disable-next-line no-prototype-builtins
					if ( ! data.hasOwnProperty( entryId ) || $.isEmptyObject( data[ entryId ] ) ) {
						continue;
					}
					entries.push( {
						// eslint-disable-next-line camelcase
						entry_id: entryId,
						entry: data[ entryId ],
					} );
				}

				return entries;
			},

			/**
			 * Remove all offline entries from a storage for a given form.
			 *
			 * @param {number} formId Form ID.
			 */
			clearAll: function( formId ) {

				this.local.namespace( parseInt( formId, 10 ) ).clearAll();
			},
		},

		/**
		 * Manage offline forms notifications.
		 *
		 * @since 1.0.0
		 */
		notifications: {

			$_form: false,
			_actions: [],

			/**
			 * Set a form that notifications will be applied to.
			 *
			 * @param {object} $form jQuery object of a form on a page with offline functionality.
			 *
			 * @returns {WPFormsOfflineForms.notifications} WPFormsOfflineForms.notifications object.
			 */
			setForm: function( $form ) {

				this.$_form = $form;

				// Always clear actions when form is defined or changed.
				this._clearActions();

				return this;
			},

			/**
			 * Set actions that might be applied to a notification.
			 *
			 * @param {object} actions Object of actions, key:value storage. If false - clears all actions.
			 *
			 * @returns {WPFormsOfflineForms.notifications} WPFormsOfflineForms.notifications object.
			 */
			setActions: function( actions ) {
				if ( ! actions ) {
					this._clearActions();

					return this;
				}

				var action;

				for ( action in actions ) {
					if ( ! actions.hasOwnProperty( action ) ) {
						continue;
					}
					this._actions.push( {
						key: action,
						label: actions[ action ]
					} );
				}

				return this;
			},

			/**
			 * Set the actions to a default empty state.
			 *
			 * @private
			 */
			_clearActions: function() {
				this._actions = [];
			},

			/**
			 * Get a template for the whole notification.
			 *
			 * @returns {*|HTMLElement} Notification template.
			 *
			 * @private
			 */
			_getNotificationTemplate: function() {
				return $( '<div class="wpforms-notice wpforms-info"></div>' );
			},

			/**
			 * Get a template for the actions block in a notification.
			 *
			 * @returns {*|HTMLElement} Actions template.
			 *
			 * @private
			 */
			_getActionsTemplate: function() {
				return $( '<div class="wpforms-notice-actions"></div>' );
			},

			/**
			 * Get a template for the single action in actions block.
			 *
			 * @returns {*|HTMLElement} Action template.
			 *
			 * @private
			 */
			_getActionTemplate: function() {
				return $( '<a class="wpforms-notice-action"></a>' );
			},

			/**
			 * Display various notifications.
			 *
			 * @param {string}  message       Message to display.
			 * @param {boolean} isDismissible Whether it's dismissible or not.
			 */
			display: function( message, isDismissible ) {

				var classDismiss = 'non-dismissible';
				if ( isDismissible ) {
					classDismiss = '';
				}

				// Populate notifications holder with text.
				var $notification = this._getNotificationTemplate()
					.addClass( classDismiss )
					.html( message );

				// Populate actions holder with links to actions.
				if ( this._actions.length ) {
					var $actions = this._getActionsTemplate(),
						action;

					for ( action in this._actions ) {

						// Add a link to the end of actions holder.
						$actions.append(
							this._getActionTemplate()
								.attr( 'href', '#' + encodeURIComponent( this._actions[ action ].key ) )
								.text( this._actions[ action ].label )
						);
					}

					// Add actions holder to the end of notification holder.
					$notification.append( $actions );
				}

				// Finally, display a notification.
				$( '.wpforms-field-container', this.$_form ).before( $notification );

				var $form = this.$_form;

				// Scroll to form top, so that user will be able to see the message.
				$( 'html, body' ).animate( {
					scrollTop: $form.offset().top - 100
				}, 750 );
			},

			/**
			 * Hide all non-dismissible notifications.
			 */
			hide: function() {
				$( '.wpforms-notice', this.$_form ).not( '.non-dismissible' ).remove();
			},

			/**
			 * Hide all notifications for a form.
			 */
			hideAll: function() {
				$( '.wpforms-notice', this.$_form ).remove();
			},
		},

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			app.populateForms();

			// Listen for future changes in connection status.
			window.addEventListener( 'online', app.connectionEvent );
			window.addEventListener( 'offline', app.connectionEvent );

			$( document ).on( 'wpformsReady', app.checkConnectivityLoop );
			$( document ).on( 'wpformsReady', app.checkOfflineRecords );
			$( document ).on( 'wpformsReady', app.saveFormDefaults );

			app.bindUIActions();
		},

		/**
		 * Get the array of forms jQuery objects that have offline functionality.
		 *
		 * @since 1.0.0
		 */
		populateForms: function() {

			$.each( wpforms_offline_forms.offline_form_ids, function( index, formId ) {
				app.forms.push( $( '#wpforms-' + formId ) );
			} );
		},

		/**
		 * Check Internet connection in a loop by accessing a static file.
		 *
		 * @since 1.0.0
		 */
		checkConnectivityLoop: function() {

			var interval = parseInt( wpforms_offline_forms.check_connection_interval, 10 );

			if ( ! app._isNaturalNumber( interval ) ) {
				return;
			}

			setInterval( function() {
				app.processConnectivityAjaxCheck( null );
			}, interval * 1000 );
		},

		/**
		 * Send an ajax request to a static file to check the Internet connection.
		 *
		 * @since 1.0.0
		 *
		 * @param {Function} callback To be called after all the checks.
		 */
		processConnectivityAjaxCheck: function( callback ) {

			var timeout = parseInt( wpforms_offline_forms.check_connection_timeout, 10 );

			if ( ! app._isNaturalNumber( timeout ) ) {
				return;
			}

			$.ajax( {
				method: 'HEAD',
				url: wpforms_offline_forms.check_connection_url,
				cache: false,
				timeout: timeout * 1000,
				complete: function( jqXHR, textStatus ) {

					if ( jqXHR.status !== 0 && textStatus !== 'timeout' ) {
						app.isOffline = false;

						app.notificationOfflineHide();
					} else {
						app.isOffline = true;

						app.notificationOfflineDisplay();
					}

					if ( typeof callback === 'function' ) {
						callback();
					}
				},
			} );
		},

		/**
		 * On form load check whether we have entries for it in our storage,
		 * that are not yet submitted. Display to a user a notification with choices.
		 *
		 * @since 1.0.0
		 */
		checkOfflineRecords: function() {

			$.each( app.forms, function( index, form ) {
				var entries = app.storage.getAll( form.find( 'form' ).data( 'formid' ) ),
					notifications = app.notifications.setForm( form );

				notifications.hideAll();

				if ( entries.length === 0 ) {
					return;
				}

				if ( entries.length === 1 ) {
					if ( ! app.isOffline ) {
						notifications.setActions( {
							offline_restore: wpforms_offline_forms.text_restore_btn,
							offline_clear: wpforms_offline_forms.text_clear_btn
						} );
					}

					notifications.display( wpforms_offline_forms.text_restore_single );
				} else {
					if ( ! app.isOffline ) {
						notifications.setActions( {
							offline_restore: wpforms_offline_forms.text_restore_btn,
							offline_clear: wpforms_offline_forms.text_clear_all_btn
						} );
					}

					notifications.display( wpforms_offline_forms.text_restore_plural );
				}
			} );
		},

		/**
		 * When form has finished loading, retrieve and save in memory all form default values.
		 *
		 * @since 1.0.0
		 */
		saveFormDefaults: function() {

			$.each( wpforms_offline_forms.offline_form_ids, function( index, formId ) {
				app.formsData[ formId ] = $( '#wpforms-' + formId ).find( 'form' ).serializeArray();
			} );
		},

		/**
		 * When form is submitted we need to clear it.
		 * We are actually restoring to its predefined state.
		 *
		 * @since 1.0.0
		 *
		 * @param {number} formId Form ID.
		 */
		processRestoreFormDefault: function( formId ) {

			var $formHolder = $( '#wpforms-' + formId );

			if (
				typeof app.formsData[ formId ] === 'undefined' ||
				! $formHolder.length
			) {
				return;
			}

			$formHolder.find( 'form' ).deserialize( app.formsData[ formId ], {
				change: app.deserializeChangeCallback,
			} );

			/*
			 * In case we have a multi-page form we need to post-process it.
			 */
			if ( $formHolder.find( '.wpforms-page-indicator' ).length ) {

				// Find a prev button to trigger navigation.
				if ( $formHolder.find( '.wpforms-page-prev[data-page="2"]' ).length ) {

					$formHolder.find( '.wpforms-page' ).hide();
					$formHolder.find( '.wpforms-page-prev[data-page="2"]' ).trigger( 'click' );

				} else {

					// Create a button that will be used to fake clicks.
					var $button = $( '<button class="wpforms-page-button wpforms-page-prev"></button>' );
					$button
						.css( 'visibility', 'hidden' )
						.data( 'action', 'prev' )
						.data( 'page', '2' )
						.data( 'formid', formId );

					$formHolder.find( '.wpforms-page' ).hide();
					$formHolder.find( '.wpforms-field-pagebreak' ).first().append( $button );
					$button.trigger( 'click' );
					$button.remove();
				}
			}

			this.checkOfflineRecords();
		},

		/**
		 * Properly set Smart Phone field value.
		 *
		 * @since 1.2.1
		 *
		 * @param {mixed} val Field value.
		 */
		deserializeChangeCallback: function( val ) {

			var $input = $( this ),
				$field = $input.closest( '.wpforms-field' );

			if ( $field.hasClass( 'wpforms-field-phone' ) && typeof $.fn.intlTelInput !== 'undefined' ) {
				var $smartPhone = $field.find( '.wpforms-smart-phone-field' );
				if ( $smartPhone.length > 0 ) {
					$smartPhone.intlTelInput( 'setNumber', val );
				}
			}

			$input.trigger( 'change' );
		},

		/**
		 * Bind actions for each individual relevant forms.
		 *
		 * @since 1.0.0
		 */
		bindUIActions: function() {

			var _ = this;

			/*
			 * Process the form submission.
			 */
			$.each( app.forms, function( index, form ) {
				_.processFormSave( form.find( 'form' ) );
			} );

			/*
			 * Process the form Submit button click.
			 * Listen event for offline-capable forms only.
			 */
			$.each( app.forms, function( index, form ) {

				// Always hard-check internet status. Submit afterwards to have status already set up.
				$( document ).on( 'click', '#' + form.attr( 'id' ) + ' .wpforms-submit-container button[type="submit"]', function( e ) {

					e.preventDefault();
					e.stopPropagation();
					e.stopImmediatePropagation();

					var $submit = $( this ),
						altText = $submit.data( 'alt-text' ),
						$form = $( this ).closest( 'form' ),
						buttonText = $submit.text();

					if ( $submit.prop( 'disabled' ) ) {
						return;
					}

					$submit.prop( 'disabled', true );

					if ( altText ) {
						$submit.text( altText );
					}

					_.processConnectivityAjaxCheck( function() {

						$submit.prop( 'disabled', false );
						$submit.text( buttonText );
						$form.submit();
					} );
				} );
			} );

			/*
			 * Process the restore functionality for a form.
			 */
			$( document ).on( 'click', '.wpforms-notice-action[href="#offline_restore"]', function( e ) {
				e.preventDefault();

				_.processRestore( $( this ).closest( 'form' ) );
			} );

			/*
			 * Process the clear functionality for a form.
			 */
			$( document ).on( 'click', '.wpforms-notice-action[href="#offline_clear"]', function( e ) {
				e.preventDefault();

				_.processClearAll( $( this ).closest( 'form' ) );
			} );
		},

		/**
		 * Applicable only if we are offline.
		 * Get all the fields values of the form, except ignored for security.
		 * Save data in the storage.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} $form jQuery object of the form.
		 */
		processFormSave: function( $form ) {

			$form.submit( function( e ) {

				// If online - proceed as usual.
				if ( ! app.isOffline ) {
					return true;
				}

				// Just do not submit, like ever.
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();

				var formId = parseInt( $form.data( 'formid' ), 10 );

				// Disable certain fields, so they won't be serialized.
				$( '.wpforms-field', $form ).each( function( index, field ) {
					if (
						$( field ).hasClass( 'wpforms-field-password' ) ||
						$( field ).hasClass( 'wpforms-field-credit-card' ) ||
						$( field ).hasClass( 'wpforms-field-signature' )
					) {
						$( field ).find( 'input, select' ).attr( 'disabled', 'disabled' );
					}
				} );

				// Serialize all the fields, disabled are ignored.
				var formData = $form.serializeArray();

				// Enable fields back, so user can continue submission.
				$( '.wpforms-field', $form ).each( function( index, field ) {
					if (
						$( field ).hasClass( 'wpforms-field-password' ) ||
						$( field ).hasClass( 'wpforms-field-credit-card' ) ||
						$( field ).hasClass( 'wpforms-field-signature' )
					) {
						$( field ).find( 'input, select' ).removeAttr( 'disabled' );
					}
				} );

				// Save to the storage.
				app.storage.set( formId, formData );

				// Restore default state of the form.
				app.processRestoreFormDefault( formId );
			} );
		},

		/**
		 * Clear all offline records from storage for this form and hide all notifications.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} $form jQuery object of a form.
		 */
		processRestore: function( $form ) {

			var entryData = this.storage.get( $form.data( 'formid' ) );

			$form.deserialize( entryData.entry, {
				change: app.deserializeChangeCallback,
			} );

			this.checkOfflineRecords();
		},

		/**
		 * Clear all offline records from storage for this form and hide all notifications.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} $form jQuery object of a form.
		 */
		processClearAll: function( $form ) {

			// Clear all offline records, whether it's 1 or 100500.
			this.storage.clearAll( $form.data( 'formid' ) );

			// Hide all notifications.
			this.notifications.setForm( $form ).hideAll();
		},

		/**
		 * What to do when user connectivity state is changed to online or offline.
		 *
		 * @since 1.0.0
		 */
		connectionEvent: function() {

			app.connectionUpdateStatus();
		},

		/**
		 * Check if we're online, set a class on <body> if not and hide/display a notification to a user.
		 *
		 * @since 1.0.0
		 */
		connectionUpdateStatus: function() {
			if ( typeof navigator.onLine === 'undefined' ) {
				return;
			}

			app.isOffline = ! navigator.onLine;

			$( 'body' ).toggleClass( 'wpforms-is-offline', app.isOffline );

			app.isOffline ? app.notificationOfflineDisplay() : app.notificationOfflineHide();
		},

		/**
		 * Display a notification about offline status of a client.
		 * Displayed only for forms with enabled offline functionality.
		 *
		 * @since 1.0.0
		 */
		notificationOfflineDisplay: function() {

			$.each( app.forms, function( index, form ) {
				var notifications = app.notifications.setForm( form );

				notifications.hideAll();
				notifications.display( wpforms_offline_forms.text_offline, true );
			} );
		},

		/**
		 * Hide all offline messages for all forms.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} $form jQuery object of a form.
		 */
		notificationOfflineHide: function( $form ) {

			this.notifications.setForm( $form ).hide();

			// As we are back online - check whether we have something already saved locally.
			this.checkOfflineRecords();
		},

		/**
		 * Check whether the number is natural.
		 *
		 * @param {number} number Number to check.
		 *
		 * @returns {boolean} Is it a natural number of not.
		 *
		 * @private
		 */
		_isNaturalNumber: function( number ) {
			return number >= 0 && Math.floor( number ) === +number;
		},
	};

	return app;

}( document, window, jQuery ) );

// Here goes the magic. http://gph.is/1KjihQe
WPFormsOfflineForms.init();
