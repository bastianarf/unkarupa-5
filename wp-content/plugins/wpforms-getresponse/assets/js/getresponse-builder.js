/* global WPForms, wpf, Choices, wpformsGetResponseBuilderVars */
'use strict';

/**
 * WPForms Providers Builder GetResponse module.
 *
 * @since 1.3.0
 */
WPForms.Admin.Builder.Providers.GetResponse = WPForms.Admin.Builder.Providers.GetResponse || ( function( document, window, $ ) {

	/**
	 * Private functions and properties.
	 *
	 * @since 1.3.0
	 *
	 * @type {object}
	 */
	var __private = {

		/**
		 * Provider holder jQuery object.
		 *
		 * @since 1.3.0
		 *
		 * @type {jQuery}
		 */
		$providerHolder: null,

		/**
		 * Provider connections jQuery object.
		 *
		 * @since 1.3.0
		 *
		 * @type {jQuery}
		 */
		$providerConnections: null,

		/**
		 * Config contains all configuration properties.
		 *
		 * @since 1.3.0
		 *
		 * @type {object.<string, *>}
		 */
		config: {

			/**
			 * List of GetResponse templates that should be compiled.
			 *
			 * @since 1.3.0
			 *
			 * @type {object}
			 */
			templates: [
				'wpforms-getresponse_v3-builder-content-connection',
				'wpforms-getresponse_v3-builder-content-connection-subscriber-subscribe',
				'wpforms-getresponse_v3-builder-content-connection-subscriber-unsubscribe',
				'wpforms-getresponse_v3-builder-content-connection-error',
				'wpforms-getresponse_v3-builder-content-connection-lock',
				'wpforms-getresponse_v3-builder-content-connection-conditionals',
			],
		},

		/**
		 * Sometimes in DOM we might have placeholders or temporary connection IDs.
		 * We need to replace them with actual values.
		 *
		 * @since 1.3.0
		 *
		 * @param {string} connectionId New connection ID to replace to.
		 * @param {object} $connection  jQuery DOM connection element.
		 */
		replaceConnectionIds: function( connectionId, $connection ) {

			// Replace old temporary %connection_id% from PHP code with the new one.
			$connection
				.find( 'input, textarea, select, label' ).each( function() {

					var $this = $( this );

					if ( $this.attr( 'name' ) ) {
						$this.attr( 'name', $this.attr( 'name' ).replace( /%connection_id%/gi, connectionId ) );
					}

					if ( $this.attr( 'id' ) ) {
						$this.attr( 'id', $this.attr( 'id' ).replace( /%connection_id%/gi, connectionId ) );
					}

					if ( $this.attr( 'for' ) ) {
						$this.attr( 'for', $this.attr( 'for' ).replace( /%connection_id%/gi, connectionId ) );
					}

					if ( $this.attr( 'data-name' ) ) {
						$this.attr( 'data-name', $this.attr( 'data-name' ).replace( /%connection_id%/gi, connectionId ) );
					}
				} );
		},

		/**
		 * Whether we have an account ID in a list of all available accounts.
		 *
		 * @since 1.3.0
		 *
		 * @param {string} connectionAccID Connection account ID to check.
		 * @param {Array}  accounts        Array of objects, usually received from API.
		 *
		 * @returns {boolean} True if connection already exists.
		 */
		connectionAccountExists: function( connectionAccID, accounts ) {

			if ( _.isEmpty( accounts ) ) {
				return false;
			}

			// New connections, that have not been saved don't have the account ID yet.
			if ( _.isEmpty( connectionAccID ) ) {
				return true;
			}

			return _.has( accounts, connectionAccID );
		},
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.3.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Current provider slug.
		 *
		 * @since 1.3.0
		 *
		 * @type {string}
		 */
		provider: 'getresponse_v3',

		/**
		 * This is a shortcut to the WPForms.Admin.Builder.Providers object,
		 * that handles the parent all-providers functionality.
		 *
		 * @since 1.3.0
		 *
		 * @type {object}
		 */
		Providers: {},

		/**
		 * This is a shortcut to the WPForms.Admin.Builder.Templates object,
		 * that handles all the template management.
		 *
		 * @since 1.3.0
		 *
		 * @type {object}
		 */
		Templates: {},

		/**
		 * This is a shortcut to the WPForms.Admin.Builder.Providers.cache object,
		 * that handles all the cache management.
		 *
		 * @since 1.3.0
		 *
		 * @type {object}
		 */
		Cache: {},

		/**
		 * This is a flag for ready state.
		 *
		 * @since 1.3.0
		 *
		 * @type {boolean}
		 */
		isReady: false,

		/**
		 * Start the engine.
		 *
		 * Run initialization on providers panel only.
		 *
		 * @since 1.3.0
		 */
		init: function() {

			// We are requesting/loading a Providers panel.
			if ( 'providers' === wpf.getQueryString( 'view' ) ) {
				$( '#wpforms-panel-providers' ).on( 'WPForms.Admin.Builder.Providers.ready', app.ready );
			}

			// We have switched to Providers panel.
			$( document ).on( 'wpformsPanelSwitched', function( e, panel ) {

				if ( 'providers' === panel ) {
					app.ready();
				}
			} );
		},

		/**
		 * Initialized once the DOM and Providers are fully loaded.
		 *
		 * @since 1.3.0
		 */
		ready: function() {

			if ( app.isReady ) {
				return;
			}

			// Done by reference, so we are not doubling memory usage.
			app.Providers = WPForms.Admin.Builder.Providers;
			app.Templates = WPForms.Admin.Builder.Templates;
			app.Cache     = app.Providers.cache;

			// Save a jQuery selector for provider holder.
			__private.$providerHolder      = app.Providers.getProviderHolder( app.provider );
			__private.$providerConnections = __private.$providerHolder.find( '.wpforms-builder-provider-connections' );

			// Register custom Underscore.js templates.
			app.Templates.add( __private.config.templates );

			// Register a handler for Add New Account process.
			app.Providers.ui.account.registerAddHandler( app.provider, app.processAccountAdd );

			// Events registration.
			app.bindUIActions();
			app.bindTriggers();

			app.processInitial();

			// Save a flag for ready state.
			app.isReady = true;
		},

		/**
		 * Process various events as a response to UI interactions.
		 *
		 * @since 1.3.0
		 */
		bindUIActions: function() {

			__private.$providerHolder
				.on( 'connectionCreate', app.connection.callbacks.create )
				.on( 'connectionDelete', app.connection.callbacks.delete )
				.on( 'change', '.js-wpforms-builder-getresponse-provider-connection-account', app.ui.account.changeCallback )
				.on( 'change', '.js-wpforms-builder-getresponse-provider-connection-action', app.ui.action.changeCallback )
				.on( 'input', '.js-wpforms-builder-getresponse-provider-tags-new', app.ui.tags.addNewInputCallback )
				.on( 'input', '.js-wpforms-builder-getresponse-provider-cycle-day', app.ui.cycleDay.inputCallback );
		},

		/**
		 * Fire certain events on certain actions, specific for related connections.
		 * These are not directly caused by user manipulations.
		 *
		 * @since 1.3.0
		 */
		bindTriggers: function() {

			__private.$providerConnections.on( 'connectionsDataLoaded', function( e, data ) {

				for ( var connectionId in data.connections ) {

					if ( ! _.has( data.connections, connectionId ) ) {
						continue;
					}

					app.connection.callbacks.generate( {
						connection: app.Cache.getById( app.provider, 'connections', connectionId ),
						conditional: app.Cache.getById( app.provider, 'conditionals', connectionId ),
					} );
				}
			} );

			__private.$providerConnections.on( 'connectionGenerated', function( e, data ) {

				var $connection = app.connection.getById( data.connection.id );

				// Run replacing temporary connection ID, if it a new connection.
				if ( _.has( data.connection, 'isNew' ) && data.connection.isNew ) {
					__private.replaceConnectionIds( data.connection.id, $connection );
				}

				$( '.js-wpforms-builder-getresponse-provider-connection-action', $connection ).trigger( 'change', [ $connection ] );
			} );

			__private.$providerConnections.on( 'connectionRendered', function( e, provider, connectionId ) {

				var $connection = app.connection.getById( connectionId );

				__private.replaceConnectionIds( connectionId, $connection );
				app.mapFields( connectionId, $connection );
				app.loadChoicesJS( $connection );
			} );
		},

		/**
		 * Compile template with data if any and display them on a page.
		 *
		 * @since 1.3.0
		 */
		processInitial: function() {

			__private.$providerConnections.prepend( app.tmpl.callbacks.commonsHTML() );
			app.connection.callbacks.dataLoad();
		},

		/**
		 * Process the account creation in FormBuilder.
		 *
		 * @since 1.3.0
		 *
		 * @param {object} modal jQuery-Confirm modal object.
		 *
		 * @returns {boolean} False.
		 */
		processAccountAdd: function( modal ) {

			var $apiKeyField = modal.$content.find( 'input[name="apikey"]' ),
				$error       = modal.$content.find( '.error' ),
				apiKey       = $apiKeyField.val().toString().trim();

			if ( _.isEmpty( apiKey ) ) {
				$error.show();
				modal.setType( 'red' );

				if ( _.isEmpty( apiKey ) ) {
					$apiKeyField.addClass( 'wpforms-error' );
				}

			} else {
				$error.hide();
				modal.setType( 'blue' );
				$apiKeyField.removeClass( 'wpforms-error' );

				app.Providers.ajax
					.request( app.provider, {
						data: {
							task: 'account_save',
							apikey: apiKey,
							label: modal.$content.find( 'input[name="label"]' ).val().toString().trim(),
						},
					} )
					.done( function( response ) {

						if (
							! response.success ||
							(
								_.has( response.data, 'error' ) &&
								! _.isEmpty( response.data.error )
							)
						) {
							modal.setType( 'red' );
							$error.html( response.data.error ).show();

						} else {

							// Hide `Add New Account` button.
							__private.$providerHolder
								.find( '.wpforms-builder-provider-title-add' )
								.toggleClass( 'hidden' );
							modal.close();
						}
					} );
			}

			return false;
		},

		/**
		 * For each connection we should preselect already saved email and name fields.
		 *
		 * @since 1.3.0
		 *
		 * @param {string} connectionId Current connection ID.
		 * @param {object} $connection  jQuery DOM connection element.
		 */
		mapFields: function( connectionId, $connection ) {

			var connection = app.Cache.getById( app.provider, 'connections', connectionId );

			if (
				_.isEmpty( connection ) ||
				_.isEmpty( connection.fields )
			) {
				return;
			}

			// Preselect email.
			if ( '' !== connection.fields.email ) {
				$( 'select[name="providers[' + app.provider + '][' + connectionId + '][fields][email]"]', $connection ).val( connection.fields.email );
			}

			// Preselect name.
			if ( '' !== connection.fields.name ) {
				$( 'select[name="providers[' + app.provider + '][' + connectionId + '][fields][name]"]', $connection ).val(
					connection.fields.name
				);
			}
		},

		/**
		 * Load Choices.js library.
		 *
		 * @since 1.3.0
		 *
		 * @param {object} $connection jQuery connection selector.
		 */
		loadChoicesJS: function( $connection ) {

			// Load if function exists.
			if ( ! _.isFunction( window.Choices ) ) {
				return;
			}

			var $selects = $( '.choicesjs-select', $connection );

			if ( ! $selects.length ) {
				return;
			}

			$selects.each( function( idx, el ) {

				var $this = $( el ),
					args  = {
						shouldSort: false,
						removeItemButton: true,
						callbackOnInit: function() {

							var self     = this,
								$element = $( self.passedElement.element ),
								$input   = $( self.input.element );

							/**
							 * If a multiple select has selected choices - hide a placeholder input.
							 * We use custom styles like `.screen-reader-text` for it,
							 * because it avoids an issue with closing a dropdown.
							 */
							if ( $element.prop( 'multiple' ) ) {

								// On init event.
								if ( self.getValue( true ).length ) {
									$input.addClass( self.config.classNames.input + '--hidden' );
								}

								// On change event.
								$element.on( 'change', function() {

									self.getValue( true ).length ? $input.addClass( self.config.classNames.input + '--hidden' ) : $input.removeClass( self.config.classNames.input + '--hidden' );
								} );
							}
						},
					};

				// Return if already initialized.
				if ( 'undefined' !== typeof $this.data( 'choicesjs' ) ) {
					return;
				}

				$this.data( 'choicesjs', new Choices( $this[0], args ) );
			} );
		},

		/**
		 * Connection property.
		 *
		 * @since 1.3.0
		 */
		connection: {

			/**
			 * Sometimes we might need to a get a connection DOM element by its ID.
			 *
			 * @since 1.3.0
			 *
			 * @param {string} connectionId Connection ID to search for a DOM element by.
			 *
			 * @returns {object} jQuery selector for connection.
			 */
			getById: function( connectionId ) {

				return __private.$providerConnections.find( '.wpforms-builder-provider-connection[data-connection_id="' + connectionId + '"]' );
			},

			/**
			 * Connection methods.
			 *
			 * @since 1.3.0
			 */
			callbacks: {

				/**
				 * Create a connection using the user entered name.
				 *
				 * @since 1.3.0
				 *
				 * @param {object} event Event object.
				 * @param {string} name  Connection name.
				 */
				create: function( event, name ) {

					var connectionId = ( new Date().getTime() ).toString( 16 ),
						connection   = {
							id: connectionId,
							name: name,
							isNew: true,
						};

					app.Cache.addTo( app.provider, 'connections', connectionId, connection );

					app.connection.callbacks.generate( {
						connection: connection,
					} );
				},

				/**
				 * Connection is deleted - delete a cache as well.
				 *
				 * @since 1.3.0
				 *
				 * @param {object} event       Event object.
				 * @param {object} $connection jQuery DOM element for a connection.
				 */
				delete: function( event, $connection ) {

					if ( ! $connection.closest( __private.$providerHolder ).length ) {
						return;
					}

					var connectionId = $connection.data( 'connection_id' );

					if ( 'undefined' !== typeof connectionId ) {
						app.Cache.deleteFrom( app.provider, 'connections', connectionId );
					}
				},

				/**
				 * Get the template and data for a connection and process it.
				 *
				 * @since 1.3.0
				 *
				 * @param {object} data Connection data.
				 */
				generate: function( data ) {

					var accounts = app.Cache.get( app.provider, 'accounts' );

					/*
					 * We may or may not receive accounts previously.
					 * If yes - render instantly, if no - request them via AJAX.
					 */
					if ( ! _.isEmpty( accounts ) ) {
						app.connection.callbacks.generateItem( data, accounts );

					} else {

						// We need to get the live list of accounts, as nothing is in cache.
						app.Providers.ajax
							.request( app.provider, {
								data: {
									task: 'accounts_get',
								},
							} )
							.done( function( response ) {

								if (
									! response.success ||
									! _.has( response.data, 'accounts' )
								) {
									return;
								}

								// Save ACCOUNTS in "cache" as a copy.
								app.Cache.set( app.provider, 'accounts', response.data.accounts );

								app.connection.callbacks.generateItem( data, response.data.accounts );
							} );
					}
				},

				/**
				 * Generatge a connection.
				 *
				 * @since 1.3.0
				 *
				 * @param {object} data Connection data.
				 * @param {object} accounts Accounts.
				 */
				generateItem: function( data, accounts ) {

					var tmplConnection  = app.Templates.get( 'wpforms-' + app.provider + '-builder-content-connection' ),
						tmplConditional = $( '#tmpl-wpforms-' +  app.provider + '-builder-content-connection-conditionals' ).length ? app.Templates.get( 'wpforms-' +  app.provider + '-builder-content-connection-conditionals' ) : app.Templates.get( 'wpforms-providers-builder-content-connection-conditionals' ),
						conditional     = ( _.has( data.connection, 'isNew' ) && data.connection.isNew ) ? tmplConditional() : data.conditional;

					if ( __private.connectionAccountExists( data.connection.account_id, accounts ) ) {
						__private.$providerConnections
							.prepend(
								tmplConnection( {
									accounts: accounts,
									connection: data.connection,
									conditional: conditional,
									provider: app.provider,
								} )
							);

						// When we are done adding a new connection with its accounts - trigger next steps.
						__private.$providerConnections.trigger( 'connectionGenerated', [ data ] );
					}
				},

				/**
				 * Fire AJAX-request to retrieve the list of all saved connections.
				 *
				 * @since 1.3.0
				 */
				dataLoad: function() {

					app.Providers.ajax
						.request( app.provider, {
							data: {
								task: 'connections_get',
							},
						} )
						.done( function( response ) {

							if (
								! response.success ||
								! _.has( response.data, 'connections' )
							) {
								return;
							}

							// Save CONNECTIONS to "cache" as a copy.
							app.Cache.set( app.provider, 'connections', jQuery.extend( {}, response.data.connections ) );

							// Save CONDITIONALS to "cache" as a copy.
							app.Cache.set( app.provider, 'conditionals', jQuery.extend( {}, response.data.conditionals ) );

							// Save ACCOUNTS to "cache" as a copy, if we have them.
							if ( ! _.isEmpty( response.data.accounts ) ) {
								app.Cache.set( app.provider, 'accounts', jQuery.extend( {}, response.data.accounts ) );
							}

							__private.$providerConnections.trigger( 'connectionsDataLoaded', [ response.data ] );
						} );
				},
			},
		},

		/**
		 * All methods that modify UI of a page.
		 *
		 * @since 1.3.0
		 */
		ui: {

			/**
			 * Account methods.
			 *
			 * @since 1.3.0
			 */
			account: {

				/**
				 * Callback-function on change event.
				 *
				 * @since 1.3.0
				 */
				changeCallback: function() {

					var $el         = $( this ),
						$connection = $el.closest( '.wpforms-builder-provider-connection' ),
						$action     = $( '.js-wpforms-builder-getresponse-provider-connection-action', $connection ),
						accountId   = $el.val();

					// Clear all connection data if account was changed.
					$( '.wpforms-builder-getresponse-provider-actions-data', $connection ).empty();
					$action.val( '' );

					// If account is empty.
					if ( '' === accountId ) {

						// Block Action select box.
						$action.prop( 'disabled', true );

					} else {
						$el.removeClass( 'wpforms-error' );

						// Unblock Action select box.
						$action.prop( 'disabled', false );
					}
				},
			},

			/**
			 * Action methods.
			 *
			 * @since 1.3.0
			 */
			action: {

				/**
				 * Callback-function on change event.
				 *
				 * @since 1.3.0
				 */
				changeCallback: function() {

					var $el          = $( this ),
						$connection  = $el.closest( '.wpforms-builder-provider-connection' ),
						connectionId = $connection.data( 'connection_id' ),
						accountId    = $connection.find( '.js-wpforms-builder-getresponse-provider-connection-account' ).val(),
						action       = $el.val();

					$el.removeClass( 'wpforms-error' );
					$( '.wpforms-builder-getresponse-provider-actions-data', $connection ).empty();

					app.actions.init( {
						'action': action,
						'target': $el,
						'account_id': accountId,
						'connection_id': connectionId,
					} );
				},
			},

			/**
			 * Tags methods.
			 *
			 * @since 1.3.0
			 */
			tags: {

				/**
				 * Callback-function on input event.
				 *
				 * @since 1.3.0
				 */
				addNewInputCallback: function() {

					// Allow English alphabet, numbers, and underscores ("_").
					this.value = this.value.replace( /[^_a-zA-Z0-9,]/g, '' );
				},
			},

			/**
			 * cycleDay methods.
			 *
			 * @since 1.3.0
			 */
			cycleDay: {

				/**
				 * Callback-function on input event.
				 *
				 * @since 1.3.0
				 */
				inputCallback: function() {

					// Allow only numbers.
					this.value = this.value.replace( /[^0-9]/g, '' );
				},
			},
		},

		/**
		 * Actions property.
		 *
		 * @since 1.3.0
		 */
		actions: {

			/**
			 * Actions initialization.
			 *
			 * @since 1.3.0
			 *
			 * @param {object} args Arguments.
			 */
			init: function( args ) {

				switch ( args.action ) {

					case 'subscriber_subscribe':
						app.actions.subscriber.subscribe.init( args );
						break;

					case 'subscriber_unsubscribe':
						app.actions.subscriber.unsubscribe.init( args );
						break;
				}
			},

			/**
			 * Subscriber property.
			 *
			 * @since 1.3.0
			 */
			subscriber: {

				/**
				 * Subscribe action.
				 *
				 * @since 1.3.0
				 */
				subscribe: {

					/**
					 * Subscribe initialization.
					 *
					 * @since 1.3.0
					 *
					 * @param {object} args Arguments.
					 */
					init: function( args ) {

						var sources = [ 'lists', 'tags', 'customFields' ],
							self    = this,
							index   = 0,
							data;

						for ( ; index < sources.length; index++ ) {
							data = app.Cache.get( app.provider, sources[ index ] );

							if (
								_.isEmpty( data ) ||
								! _.has( data, args.account_id )
							) {
								self.request( args );
								return;
							}
						}

						this.render( args );
					},

					/**
					 * AJAX request.
					 *
					 * @since 1.3.0
					 *
					 * @param {object} args Arguments.
					 */
					request: function( args ) {

						var self = this;

						// Make ajax request to get lists and tags.
						app.Providers.ajax
							.request( app.provider, {
								data: {
									'task': 'subscribe_data_get',
									'account_id': args.account_id,
									'connection_id': args.connection_id,
									'sources': {
										'lists': true,
										'tags': true,
										'customFields': true,
									},
								},
							} )
							.done( function( response ) {

								if (
									! response.success ||
									_.isEmpty( response.data )
								) {
									return;
								}

								// Cache response data.
								self.cache( response.data, args );

								// Render template.
								self.render( args );
							} );
					},

					/**
					 * Render HTML.
					 *
					 * @since 1.3.0
					 *
					 * @param {object} args Arguments.
					 */
					render: function( args ) {

						var tmplSubscribe = app.Templates.get( 'wpforms-' + app.provider + '-builder-content-connection-subscriber-subscribe' ),
							tmplFields    = app.tmpl.callbacks.customFieldsHTML( args ),
							$connection   = app.connection.getById( args.connection_id );

						// Display compiled template with custom data.
						$connection
							.find( '.wpforms-builder-getresponse-provider-actions-data' )
							.html(
								tmplSubscribe( {
									connection: app.Cache.getById( app.provider, 'connections', args.connection_id ),
									lists: app.Cache.getById( app.provider, 'lists', args.account_id ),
									tags: app.Cache.getById( app.provider, 'tags', args.account_id ),
									provider: app.provider,
								} ) + tmplFields
							);

						__private.$providerConnections.trigger( 'connectionRendered', [ app.provider, args.connection_id ] );
					},

					/**
					 * Cache response data.
					 *
					 * @since 1.3.0
					 *
					 * @param {object} data Response data.
					 * @param {object} args Arguments.
					 */
					cache: function( data, args ) {

						var keys = [ 'lists', 'tags', 'customFields' ];

						$.each( keys, function( idx, key ) {

							// "Register" cache keys.
							if ( 'undefined' === typeof app.Cache.get( app.provider, key ) ) {
								app.Cache.set( app.provider, key, {} );
							}

							// Save data to cache by keys.
							if ( _.has( data, key ) ) {
								app.Cache.addTo( app.provider, key, args.account_id, data[ key ] );
							}
						} );
					},
				},

				/**
				 * Unsubscribe action.
				 *
				 * @since 1.3.0
				 */
				unsubscribe: {

					/**
					 * Unsubscribe initialization.
					 *
					 * @since 1.3.0
					 *
					 * @param {object} args Arguments.
					 */
					init: function( args ) {

						var lists = app.Cache.get( app.provider, 'lists' );

						if (
							_.isEmpty( lists ) ||
							! _.has( lists, args.account_id )
						) {
							this.request( args );

						} else {
							this.render( args );
						}
					},

					/**
					 * AJAX request.
					 *
					 * @since 1.3.0
					 *
					 * @param {object} args Arguments.
					 */
					request: function( args ) {

						var self = this;

						// Make ajax request to get lists.
						app.Providers.ajax
							.request( app.provider, {
								data: {
									'task': 'subscribe_data_get',
									'account_id': args.account_id,
									'connection_id': args.connection_id,
									'sources': {
										'lists': true,
									},
								},
							} )
							.done( function( response ) {

								if (
									! response.success ||
									_.isEmpty( response.data )
								) {
									return;
								}

								// Cache response data.
								self.cache( response.data, args );

								// Render template.
								self.render( args );
							} );
					},

					/**
					 * Render HTML.
					 *
					 * @since 1.3.0
					 *
					 * @param {object} args Arguments.
					 */
					render: function( args ) {

						var tmpl = app.Templates.get( 'wpforms-getresponse_v3-builder-content-connection-subscriber-unsubscribe' );

						app.connection.getById( args.connection_id )
							.find( '.wpforms-builder-getresponse-provider-actions-data' )
							.html(
								tmpl( {
									connection: app.Cache.getById( app.provider, 'connections', args.connection_id ),
									lists: app.Cache.getById( app.provider, 'lists', args.account_id ),
									provider: app.provider,
								} )
							);

						__private.$providerConnections.trigger( 'connectionRendered', [ app.provider, args.connection_id ] );
					},

					/**
					 * Cache response data.
					 *
					 * @since 1.3.0
					 *
					 * @param {object} data Response data.
					 * @param {object} args Arguments.
					 */
					cache: function( data, args ) {

						// "Register" cache keys.
						if ( 'undefined' === typeof app.Cache.get( app.provider, 'lists' ) ) {
							app.Cache.set( app.provider, 'lists', {} );
						}

						// Save data to cache by keys.
						if ( _.has( data, 'lists' ) ) {
							app.Cache.addTo( app.provider, 'lists', args.account_id, data.lists );
						}
					},
				},
			},
		},

		/**
		 * All methods for *.tmpl files.
		 *
		 * @since 1.3.0
		 */
		tmpl: {

			/**
			 * Wrap functions for quickly compile *.tmpl files and receive their HTML.
			 *
			 * @since 1.3.0
			 */
			callbacks: {

				/**
				 * Compile and retrieve a HTML for common elements.
				 *
				 * @since 1.3.0
				 *
				 * @returns {string} Compiled HTML.
				 */
				commonsHTML: function() {

					var tmplError = app.Templates.get( 'wpforms-' + app.provider + '-builder-content-connection-error' ),
						tmplLock  = app.Templates.get( 'wpforms-' + app.provider + '-builder-content-connection-lock' );

					return tmplError() + tmplLock( { provider: app.provider } );
				},

				/**
				 * Compile and retrieve a HTML for "Custom Fields Table".
				 *
				 * @since 1.3.0
				 *
				 * @param {object} args Arguments
				 *
				 * @returns {string} Compiled HTML.
				 */
				customFieldsHTML: function( args ) {

					var tmplFields  = app.Templates.get( 'wpforms-providers-builder-content-connection-fields' );

					return tmplFields( {
						connection: app.Cache.getById( app.provider, 'connections', args.connection_id ),
						fields: wpf.getFields(),
						provider: {
							slug: app.provider,
							placeholder: wpformsGetResponseBuilderVars.i18n.providerPlaceholder,
							fields: app.Cache.getById( app.provider, 'customFields', args.account_id ),
						},
					} );
				},
			},
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPForms.Admin.Builder.Providers.GetResponse.init();
