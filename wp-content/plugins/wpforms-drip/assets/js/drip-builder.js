/* global WPForms, jQuery, wpforms_builder, wpforms_builder_providers, _, wpf, WPFormsBuilder */

/**
 * WPForms Providers Builder Drip module.
 *
 * @since 1.0.0
 */
WPForms.Admin.Builder.Providers.Drip = WPForms.Admin.Builder.Providers.Drip || (function ( document, window, $ ) {

	'use strict';

	/**
	 * Private functions and properties.
	 *
	 * @since 1.0.0
	 *
	 * @type {Object}
	 */
	var __private = {

		/**
		 * Config contains all configuration properties.
		 *
		 * @since 1.0.0
		 *
		 * @type {Object.<string, *>}
		 */
		config: {
			/**
			 * List of Drip templates that should be compiled.
			 *
			 * @since 1.0.0
			 * @since {VERSION} Added `wpforms-drip-builder-content-connection-conditionals` template.
			 *
			 * @type {string[]}
			 */
			templates: [
				'wpforms-drip-builder-content-connection',
				'wpforms-drip-builder-content-connection-events',
				'wpforms-drip-builder-content-connection-subscriber-delete',
				'wpforms-drip-builder-content-connection-subscriber-subscribe',
				'wpforms-drip-builder-content-connection-campaigns-add',
				'wpforms-drip-builder-content-connection-campaigns-delete',
				'wpforms-drip-builder-content-connection-conditionals',
			]
		},

		/**
		 * Sometimes in DOM we might have placeholders or temporary connection IDs.
		 * We need to replace them with actual values.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} $el jQuery DOM element.
		 * @param {string} connection_id New connection ID to replace to.
		 */
		replaceConnectionIds: function ( $el, connection_id ) {
			// Replace old temporary %connection_id% from PHP code with the new one.
			$el.parents( '.wpforms-builder-provider-connection' )
			   .find( 'input, textarea, select, label' ).each( function () {
				if ( $( this ).attr( 'name' ) ) {
					$( this ).attr( 'name', $( this ).attr( 'name' ).replace( /%connection_id%/gi, connection_id ) );
				}
				if ( $( this ).attr( 'id' ) ) {
					$( this ).attr( 'id', $( this ).attr( 'id' ).replace( /%connection_id%/gi, connection_id ) );
				}
				if ( $( this ).attr( 'for' ) ) {
					$( this ).attr( 'for', $( this ).attr( 'for' ).replace( /%connection_id%/gi, connection_id ) );
				}
				if ( $( this ).attr( 'data-name' ) ) {
					$( this ).attr( 'data-name', $( this ).attr( 'data-name' ).replace( /%connection_id%/gi, connection_id ) );
				}
			} );
		},

		/**
		 * Sometimes we might need to a get a connection DOM element by its ID.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} connection_id Connection ID to search for a DOM element by.
		 */
		getConnectionById: function ( connection_id ) {
			return __private.config.$holder.find( '.wpforms-builder-provider-connection[data-connection_id="' + connection_id + '"]' );
		},

		/**
		 * Whether we have an account ID in a list of all available accounts.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} account_id Connection account ID to check.
		 * @param {Array} accounts Array of objects, usually received from Drip API.
		 *
		 * @returns boolean
		 */
		connectionAccountExists: function ( account_id, accounts ) {

			// New connections, that have not been saved don't have the account ID yet.
			if ( _.isEmpty( account_id ) ) {
				return true;
			}

			var exists = _.find( accounts, function ( account ) {
				var is_there = false;
				var i, length;

				for ( i = 0, length = account.length; i < length; i ++ ) {
					if ( _.isMatch( account[ i ], { id: account_id } ) ) {
						is_there = true;
						break;
					}
				}

				return is_there;
			} );

			return typeof exists !== 'undefined';
		}
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.0.0
	 *
	 * @type {Object}
	 */
	var app = {

		/**
		 * Current provider slug.
		 *
		 * @since 1.0.1
		 *
		 * @type {string}
		 */
		provider: 'drip',

		/**
		 * This is a shortcut to the WPForms.Admin.Builder.Providers object,
		 * that handles the parent all-providers functionality.
		 *
		 * @since 1.0.0
		 *
		 * @type {Object}
		 */
		Providers: {},

		/**
		 * This is a shortcut to the WPForms.Admin.Builder.Providers.cache object,
		 * that handles all the cache management.
		 *
		 * @since 1.0.0
		 *
		 * @type {Object}
		 */
		Cache: {},

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function () {

			// Done by reference, so we are not doubling memory usage.
			app.Providers = WPForms.Admin.Builder.Providers;
			app.Templates = WPForms.Admin.Builder.Templates;
			app.Cache     = app.Providers.cache;

			$( '#wpforms-panel-providers' ).on( 'WPForms.Admin.Builder.Providers.ready', app.ready );
		},

		/**
		 * Initialized once the DOM and Providers are fully loaded.
		 *
		 * @since 1.0.0
		 */
		ready: function () {

			__private.config.$holder = $( '#drip-provider' ).find( '.wpforms-builder-provider-body' );

			app.Templates.add( __private.config.templates );

			app.Providers.ui.account.registerAddHandler( app.provider, app.processAccountAdd );

			app.processInitialTemplates();

			app.bindUIActions();
			app.bindTriggers();
		},

		/**
		 * Compile template with data if any and display them on a page.
		 *
		 * @since 1.0.0
		 */
		processInitialTemplates: function () {

			/*
			 * Connections that are already saved.
			 */
			app.Providers.ajax
				.request( app.provider, {
				   data: {
					   task: 'connections_get'
				   }
			   } )
			   .done( function ( response ) {
				   if ( ! response.success ) {
					   return;
				   }

				   if ( ! response.data.hasOwnProperty( 'connections' ) ) {
					   return;
				   }

					// Save CONNECTIONS to "cache" as a copy.
					app.Cache.set( app.provider, 'connections', jQuery.extend( {}, response.data.connections ) );

					// Save CONDITIONALS to "cache" as a copy.
					app.Cache.set( app.provider, 'conditionals', jQuery.extend( {}, response.data.conditionals ) );

				   // Save ACCOUNTS to "cache" as a copy, if we have them.
				   if ( ! _.isEmpty( response.data, 'accounts' ) ) {
						app.Cache.set( app.provider, 'accounts', jQuery.extend( {}, response.data.accounts ) );
				   }

				   var connectionsCount = 0;

				   // Does nothing if there are no current connections for a form.
				   for ( var connection_id in response.data.connections ) {
					   if ( ! response.data.connections.hasOwnProperty( connection_id ) ) {
						   continue;
					   }

					   app.connectionGenerate( {
							connection: app.Cache.getById( app.provider, 'connections', connection_id ),
							conditional: app.Cache.getById( app.provider, 'conditionals', connection_id ),
					   } );

					   connectionsCount++;
				   }
			   } );
		},

		/**
		 * Process the account creation on FormBuilder.
		 *
		 * @since 1.1.0
		 *
		 * @param {Object} modal jQuery-Confirm modal object.
		 *
		 * @return boolean
		 */
		processAccountAdd: function ( modal ) {

			var acc_name = $.trim( modal.$content.find( 'input[name="account_name"]' ).val() ),
				acc_token = $.trim( modal.$content.find( 'input[name="api_token"]' ).val() ),
				error = modal.$content.find( '.error' );

			if ( _.isEmpty( acc_token ) ) {
				// Display an error if we have it in DOM.
				error.show();
				modal.setType( 'red' );
				modal.$content.find( 'input[name="api_token"]' ).addClass( 'wpforms-error' );

				return false;
			} else {
				modal.setType( 'blue' );
				error.hide();

				app.Providers.ajax
					.request( app.provider, {
					   data: {
						   task: 'account_save',
						   acc_name: acc_name,
						   acc_token: acc_token
					   }
				   } )
				   .done( function ( response ) {
						if ( response.success ) {

							if ( _.isEmpty( response.data.error ) ) {
								app.Providers.getProviderHolder( app.provider ).find( '.wpforms-builder-provider-title-add' ).toggleClass( 'hidden' );
								modal.close();
							} else {
								modal.setType( 'red' );
								error.html( response.data.error ).show();
							}

						} else {
							modal.setType( 'red' );
							error.show();
							console.log( response );
					   }
				   } );

				return false;
			}
		},

		/**
		 * Process various events as a response to UI interactions.
		 *
		 * @since 1.0.0
		 */
		bindUIActions: function () {

			var $e_holder = $( '#drip-provider' );

			// CONNECTION: NEW.
			$e_holder.on( 'connectionCreate', function ( e, name ) {
				app.connectionCreate( name );
			} );

			// CONNECTION: DELETE.
			$( '.wpforms-builder-provider' ).on( 'connectionDelete', function ( e, $cur_notification ) {
				if ( ! $cur_notification.parents( $e_holder ).length ) {
					return;
				}

				var connection_id = $cur_notification.data( 'connection_id' );
				if ( typeof connection_id !== 'undefined' ) {
					app.Cache.deleteFrom( app.provider, 'connections', connection_id );
				}
			} );

			// CONNECTION: ACCOUNT.
			$e_holder.on( 'change', '.js-wpforms-builder-drip-provider-connection-account', function () {
				var $connection = $( this ).parents( '.wpforms-builder-provider-connection' ),
					option_id = app.connectionGetOptionId( $connection );

				// Clear all connection data if account is empty.
				if ( $( this ).val() === '' ) {
					$( '.wpforms-builder-drip-provider-actions-data', $connection ).empty();
					$( '.js-wpforms-builder-drip-provider-connection-action', $connection )
						.attr( 'disabled', true )
						.val( '' );
					return;
				}

				// Unblock Action select box.
				$( '.js-wpforms-builder-drip-provider-connection-action', $connection ).removeAttr( 'disabled' );

				// We need to save this, so later we will be able to map easily connection_id to option_id.
				$connection.find( '.wpforms-builder-provider-connection-option_id' ).val( option_id );

				__private.config.$holder.trigger( 'accountChanged', [ option_id, $connection ] );
			} );

			// CONNECTION: ACTION.
			$e_holder.on( 'change', '.js-wpforms-builder-drip-provider-connection-action', function () {
				var $el = $( this ),
					$connection = $( this ).parents( '.wpforms-builder-provider-connection' ),
					connection_id = $connection.data( 'connection_id' ),
					option_id = app.connectionGetOptionId( $connection ),
					account_id = $connection.find( '.wpforms-builder-drip-provider-accounts select' ).val(),
					action = $( this ).val();

				$( '.wpforms-builder-drip-provider-actions-data', $connection ).empty();

				switch ( action ) {
					case 'event':
						// Make ajax request to get all events.
						app.Providers.ajax
							.request( app.provider, {
							   data: {
								   connection_account_id: account_id,
								   connection_option_id: option_id,
								   connection_id: connection_id,
								   connection_action: action,
								   task: 'events_get'
							   }
						   } )
						   .done( function ( response ) {
							   if ( ! response.data.hasOwnProperty( 'events' ) ) {
								   return;
							   }

								// Save EVENTS to "cache" as a copy.
								app.Cache.set( app.provider, 'events', response.data.events );

							   // Get own templates.
							   var tmpl_events = app.Templates.get( 'wpforms-drip-builder-content-connection-events' );
							   var tmpl_fields = app.Templates.get( 'wpforms-providers-builder-content-connection-fields' );

								// Display compiled template with custom data.
								$connection
									.find( '.wpforms-builder-drip-provider-actions-data' )
									.html(
										tmpl_events( {
											connection: app.Cache.getById( app.provider, 'connections', connection_id ),
											events: app.Cache.getById( app.provider, 'events', connection_id ),
										} ) +
										tmpl_fields( {
											connection: app.Cache.getById( app.provider, 'connections', connection_id ),
											fields: wpf.getFields(),
											provider: {
												slug: app.provider,
											},
										} )
									);

							   __private.replaceConnectionIds( $el, connection_id );

							   app.mapEmailsField( connection_id, $connection );

							   $( '#wpforms-panel-providers' ).trigger( 'connectionRendered', [ 'drip', connection_id ] );
						   } );
						break;

					case 'subscriber_delete':
						// Get own template.
						var tmpl_delete = app.Templates.get( 'wpforms-drip-builder-content-connection-subscriber-delete' );

						// Display compiled template with custom data.
						$connection
							.find( '.wpforms-builder-drip-provider-actions-data' )
							.html(
								tmpl_delete( {
									connection: app.Cache.getById( app.provider, 'connections', connection_id ),
								} )
							);

						__private.replaceConnectionIds( $el, connection_id );

						app.mapEmailsField( connection_id, $connection );


						$( '#wpforms-panel-providers' ).trigger( 'connectionRendered', [ 'drip', connection_id ] );
						break;

					case 'subscriber_subscribe':
						// Get own templates.
						var tmpl_subscribe = app.Templates.get( 'wpforms-drip-builder-content-connection-subscriber-subscribe' );
						var tmpl_fields = app.Templates.get( 'wpforms-providers-builder-content-connection-fields' );

						// Display compiled templates with custom data.
						$connection
							.find( '.wpforms-builder-drip-provider-actions-data' )
							.html(
								tmpl_subscribe( {
									connection: app.Cache.getById( app.provider, 'connections', connection_id ),
								} ) +
								tmpl_fields( {
									connection: app.Cache.getById( app.provider, 'connections', connection_id ),
									fields: wpf.getFields(),
									provider: {
										slug: app.provider,
									},
								} )
							);

						__private.replaceConnectionIds( $el, connection_id );

						app.mapEmailsField( connection_id, $connection );

						$( '#wpforms-panel-providers' ).trigger( 'connectionRendered', [ 'drip', connection_id ] );
						break;

					case 'campaign_sub':
						// Make ajax request to get all campaigns.
						app.Providers.ajax
							.request( app.provider, {
							   data: {
								   connection_account_id: account_id,
								   connection_option_id: option_id,
								   connection_id: connection_id,
								   connection_action: action,
								   task: 'campaigns_get'
							   }
						   } )
						   .done( function ( response ) {
							   if ( ! response.data.hasOwnProperty( 'campaigns' ) ) {
								   return;
							   }

								// Save CAMPAIGNS to "cache" as a copy.
								app.Cache.set( app.provider, 'campaigns', response.data.campaigns );

							   // Get own templates.
							   var tmpl_campaigns = app.Templates.get( 'wpforms-drip-builder-content-connection-campaigns-add' );
							   var tmpl_subscribe = app.Templates.get( 'wpforms-drip-builder-content-connection-subscriber-subscribe' );
							   var tmpl_fields = app.Templates.get( 'wpforms-providers-builder-content-connection-fields' );

								// Display compiled templates with custom data.
								$connection
									.find( '.wpforms-builder-drip-provider-actions-data' )
									.html(
										tmpl_campaigns( {
											connection: app.Cache.getById( app.provider, 'connections', connection_id ),
											campaigns: app.Cache.getById( app.provider, 'campaigns', connection_id ),
										} ) +
										tmpl_subscribe( {
											connection: app.Cache.getById( app.provider, 'connections', connection_id ),
											ignore: [ 'new_email',
												'ip_address',
												'tags_delete' ],
										} ) +
										tmpl_fields( {
											connection: app.Cache.getById( app.provider, 'connections', connection_id ),
											fields: wpf.getFields(),
											provider: {
												slug: app.provider,
											},
										} )
									);

							   __private.replaceConnectionIds( $el, connection_id );

							   app.mapEmailsField( connection_id, $connection );

							   $( '#wpforms-panel-providers' ).trigger( 'connectionRendered', [ 'drip', connection_id ] );
						   } );
						break;

					case 'campaign_unsub':
						// Make ajax request to get all campaigns.
						app.Providers.ajax
							.request( app.provider, {
							   data: {
								   connection_account_id: account_id,
								   connection_option_id: option_id,
								   connection_id: connection_id,
								   connection_action: action,
								   task: 'campaigns_get'
							   }
						   } )
						   .done( function ( response ) {
							   if ( ! response.data.hasOwnProperty( 'campaigns' ) ) {
								   return;
							   }

								// Save CAMPAIGNS to "cache" as a copy.
								app.Cache.set( app.provider, 'campaigns', response.data.campaigns );

							   // Get own template.
							   var tmpl_campaigns_del = app.Templates.get( 'wpforms-drip-builder-content-connection-campaigns-delete' );

							   // Display compiled template with custom data.
							   $connection
								   .find( '.wpforms-builder-drip-provider-actions-data' )
								   .html(
									   tmpl_campaigns_del( {
											connection: app.Cache.getById( app.provider, 'connections', connection_id ),
											campaigns: app.Cache.getById( app.provider, 'campaigns', connection_id ),
									   } )
								   );

							   __private.replaceConnectionIds( $el, connection_id );

							   app.mapEmailsField( connection_id, $connection );

							   $( '#wpforms-panel-providers' ).trigger( 'connectionRendered', [ 'drip', connection_id ] );
						   } );
						break;
				}

				__private.config.$holder.trigger( 'actionChanged', [ connection_id, $connection, action ] );
			} );

			// CONNECTION: EVENT - NEW.
			$e_holder.on( 'click', '.js-wpforms-builder-drip-provider-connection-event-new', function ( e ) {
				e.preventDefault();

				var $connection = $( this ).parents( '.wpforms-builder-provider-connection' );

				// Reset currently selected event, as Drip supports only 1 event name. So it's either new or old.
				$( '.js-wpforms-builder-drip-provider-connection-event', $connection ).val( '' );
				app.connectionEventNew( $connection );
			} );

			// CONNECTION: PROSPECT - CHECKED.
			$e_holder.on( 'click', '.wpforms-builder-drip-provider-prospect-check input[type="checkbox"]', function () {
				var $prospect_wrap = $( this ).parents( '.wpforms-builder-drip-provider-prospect' );

				$prospect_wrap.find( '.wpforms-builder-drip-provider-prospect-score' ).toggleClass( 'hidden' );
			} );
		},

		/**
		 * Fire certain events on certain actions, specific for related connections.
		 * These are not directly caused by user manipulations.
		 *
		 * @since 1.0.0
		 */
		bindTriggers: function () {

			__private.config.$holder.on( 'connectionGenerated', function ( e, data ) {
				var $connection = __private.getConnectionById( data.connection.id );

				$( '.js-wpforms-builder-drip-provider-connection-account', $connection ).trigger( 'change', [ $connection ] );
				$( '.js-wpforms-builder-drip-provider-connection-action', $connection ).trigger( 'change', [ $connection ] );
			} );

			// Properly handle conditional logic DOM modification.
			__private.config.$holder.on( 'DOMNodeInsertedIntoDocument DOMNodeRemovedFromDocument', '.wpforms-conditional-block-panel', function () {
				__private.replaceConnectionIds(
					$( this ),
					$( this ).parents( '.wpforms-builder-provider-connection' ).data( 'connection_id' )
				);
			} );
		},

		/**
		 * Create a connection using the user entered name.
		 *
		 * @since 1.0.0
		 */
		connectionCreate: function ( name ) {

			var connection_id = (new Date().getTime()).toString( 16 ),
				connection = {
					id: connection_id,
					name: name,
					isNew: true
				};

			app.Cache.addTo( app.provider, 'connections', connection_id, connection );

			app.connectionGenerate( {
				connection: connection
			} );
		},

		/**
		 * Get the template and data for a connection and process it.
		 *
		 * @since 1.0.0
		 */
		connectionGenerate: function ( data ) {

			var tmplConnection  = app.Templates.get( 'wpforms-drip-builder-content-connection' ),
				tmplConditional = $( '#tmpl-wpforms-drip-builder-content-connection-conditionals' ).length ? app.Templates.get( 'wpforms-drip-builder-content-connection-conditionals' ) : app.Templates.get( 'wpforms-providers-builder-content-connection-conditionals' ),
				conditional     = ( Object.prototype.hasOwnProperty.call( data.connection, 'isNew' ) && data.connection.isNew ) ? tmplConditional() : data.conditional,
				accounts        = app.Cache.get( app.provider, 'accounts' ); // Array of account objects.

			/*
			 * We may or may not receive accounts previously.
			 * If yes - render instantly, if no - request them via AJAX.
			 */
			if ( ! _.isEmpty( accounts ) ) {
				if ( __private.connectionAccountExists( data.connection.account_id, accounts ) ) {
					__private.config.$holder
						.find( '.wpforms-builder-provider-connections' )
						.prepend(
							tmplConnection( {
								connection: data.connection,
								accounts: accounts,
								conditional: conditional,
							} )
						);

					// When we are done adding a new connection with its accounts - trigger next steps.
					__private.config.$holder.trigger( 'connectionGenerated', [ data ] );
				}
			}
			else {
				// We need to get the live list of accounts, as nothing is in cache.
				app.Providers.ajax
					.request( app.provider, {
					   data: {
						   task: 'accounts_get'
					   }
				   } )
				   .done( function ( response ) {
					   if ( ! response.data.hasOwnProperty( 'accounts' ) ) {
						   return;
					   }

						// Save ACCOUNTS in "cache" as a copy.
						app.Cache.set( app.provider, 'accounts', response.data.accounts );

					   if ( __private.connectionAccountExists( data.connection.account_id, response.data.accounts ) ) {
						   __private.config.$holder
								.find( '.wpforms-builder-provider-connections' )
								.prepend(
									tmplConnection( {
										connection: data.connection,
										accounts: response.data.accounts,
										conditional: conditional,
									} )
								);

						   // When we are done adding a new connection with its accounts - trigger next steps.
						   __private.config.$holder.trigger( 'connectionGenerated', [ data ] );
					   }
				   } );
			}
		},

		/**
		 * Show/hide new custom event field.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} $connection jQuery DOM connection element.
		 */
		connectionEventNew: function ( $connection ) {

			var $new_input = $connection.find( '.wpforms-builder-drip-provider-events-new' );

			if ( $new_input.is( ':visible' ) ) {
				// Clear and hide.
				$new_input.hide( 400, function () {
					$new_input.find( 'input' ).val( '' );
				} );
			}
			else {
				// Display.
				$new_input.show( 'fast', function () {
					$( this ).find( 'input' ).focus();
				} );
			}
		},

		/**
		 * For each connection we should preselect already saved email field.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} connection_id Current connection ID.
		 * @param {Object} $connection jQuery DOM connection element.
		 */
		mapEmailsField: function ( connection_id, $connection ) {

			var connection = app.Cache.getById( app.provider, 'connections', connection_id );

			// Now we need to map fields from connections to events fields.
			if (
				! _.isEmpty( connection ) &&
				! _.isEmpty( connection.fields ) &&
				(
					! _.isEmpty( connection.fields.email ) ||
					! _.isEmpty( connection.fields.new_email )
				)
			) {
				$( 'select[name="providers[drip][' + connection_id + '][fields][email]"]', $connection ).val(
					connection.fields.email
				);
				$( 'select[name="providers[drip][' + connection_id + '][fields][new_email]"]', $connection ).val(
					connection.fields.new_email
				);
			}
		},

		/**
		 * From time to time, based on actions, we need option_id, that is stored in a hidden field.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} $connection jQuery DOM connection element.
		 */
		connectionGetOptionId: function ( $connection ) {
			return $connection.find( '.wpforms-builder-drip-provider-accounts select :selected' ).data( 'option_id' );
		}
	};

	// Provide access to public functions/properties.
	return app;

})( document, window, jQuery );

// Initialize.
WPForms.Admin.Builder.Providers.Drip.init();
