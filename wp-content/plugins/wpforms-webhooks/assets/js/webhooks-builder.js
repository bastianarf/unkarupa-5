/* global wpforms_builder, wpf */
'use strict';

var WPFormsBuilderWebhooks = window.WPFormsBuilderWebhooks || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 1.0.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * jQuery selector for holder.
		 *
		 * @since 1.0.0
		 *
		 * @type {object}
		 */
		$holder: $( '.wpforms-panel-content-section-webhooks' ),

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			// Do that when DOM is ready.
			$( document ).ready( app.ready );
		},

		/**
		 * DOM is fully loaded.
		 *
		 * @since 1.0.0
		 */
		ready: function() {

			app.events();
		},

		/**
		 * Register JS events.
		 *
		 * @since 1.0.0
		 */
		events: function() {

			$( '#wpforms-builder' )
				.on( 'wpformsSaved', app.requiredFields.init )
				.on( 'wpformsSettingsBlockAdded', app.webhookBlockAdded )
				.on( 'wpformsFieldMapTableAddedRow', app.fieldMapTableAddRow )
				.on( 'change.wpformsWebhooks', '#wpforms-panel-field-settings-webhooks_enable', app.webhooksToggle )
				.on( 'input.wpformsWebhooks', '.wpforms-field-map-table .http-key-source', app.updateNameAttr );

			app.$holder
				.on( 'change.wpformsWebhooks', '.wpforms-field-map-table .wpforms-field-map-select', app.changeSourceSelect )
				.on( 'change.wpformsWebhooks', '.wpforms-field-map-table .wpforms-field-map-is-secure-checkbox', app.changeSecure )
				.on( 'click.wpformsWebhooks', '.wpforms-field-map-table .wpforms-field-map-custom-value-close', app.closeCustomValue );
		},

		/**
		 * Resetting fields when we add a new webhook.
		 *
		 * @since {VERSION}
		 *
		 * @param {object} event  Event object.
		 * @param {object} $block New Webhook block.
		 */
		webhookBlockAdded: function( event, $block ) {

			if ( ! $block.length || 'webhook' !== $block.data( 'block-type' ) ) {
				return;
			}

			$block.find( '.wpforms-field-map-table .wpforms-field-map-custom-value-close' ).trigger( 'click' );
		},

		/**
		 * Resetting fields when we add a new table row for mapping.
		 *
		 * @since {VERSION}
		 *
		 * @param {object} event   Event object.
		 * @param {object} $block  jQuery selector on Webhook block.
		 * @param {object} $choice jQuery selector on new table row.
		 */
		fieldMapTableAddRow: function( event, $block, $choice ) {

			if ( ! $block.length || 'webhook' !== $block.data( 'block-type' ) || ! $choice.length ) {
				return;
			}

			$choice.find( '.wpforms-field-map-custom-value-close' ).trigger( 'click' );
		},

		/**
		 * Toggle the displaying webhook settings depending on if the
		 * webhooks are enabled.
		 *
		 * @since 1.0.0
		 */
		webhooksToggle: function() {

			app.$holder
				.find( '.wpforms-builder-settings-block-webhook, .wpforms-webooks-add' )
				.toggleClass( 'hidden', '0' === $( this ).val() );
		},

		/**
		 * Field map table, update key source.
		 *
		 * @since 1.0.0
		 */
		updateNameAttr: function() {

			var $this    = $( this ),
				value    = $this.val(),
				$row     = $this.closest( 'tr' ),
				$targets = $row.find( '.wpforms-field-map-select' ),
				name     = $targets.data( 'name' );

			if ( ! value && '' !== value ) {
				return;
			}

			if ( $row.find( 'td.field' ).hasClass( 'field-is-custom-value' ) ) {
				$targets = $row.find( '.wpforms-field-map-custom-value, .wpforms-field-map-is-secure-checkbox' );
			}

			$targets.each( function( idx, field ) {

				var newName = name + $( field ).data( 'suffix' );

				// Allow characters (lowercase and uppercase), numbers, decimal point, underscore and minus.
				$( field ).attr( 'name', newName.replace( '{source}', value.replace( /[^a-zA-Z0-9._-]/gi, '' ) ) );
			} );
		},

		/**
		 * Event-callback when the source select was changed on "Add Custom Value".
		 *
		 * @since {VERSION}
		 */
		changeSourceSelect: function() {

			var $row          = $( this ).closest( 'tr' ),
				isCustomValue = ( this.value && this.value === 'custom_value' );

			if ( isCustomValue ) {
				$( this ).attr( 'name', '' );
				$row.find( 'td.field' ).toggleClass( 'field-is-custom-value', isCustomValue );
				$row.find( '.http-key-source' ).trigger( 'input.wpformsWebhooks' );
			}
		},

		/**
		 * Change a "Custom Value" input type - `text` to `password` or vice versa.
		 *
		 * @param {object} event Event object.
		 */
		changeSecure: function( event ) {

			var $row = $( this ).closest( 'tr' );

			$row.find( '.wpforms-field-map-custom-value' ).attr( 'type', event.target.checked ? 'password' : 'text' );
		},

		/**
		 * Event-callback when the close button for "Custom Value" was clicked.
		 *
		 * @since {VERSION}
		 *
		 * @param {object} event Event object.
		 */
		closeCustomValue: function( event ) {

			var $row = $( this ).closest( 'tr' );

			event.preventDefault();

			$row.find( 'td.field' ).removeClass( 'field-is-custom-value' );
			$row.find( '.wpforms-field-map-select' ).prop( 'selectedIndex', 0 );
			$row.find( '.wpforms-field-map-is-secure-checkbox' )
				.attr( 'name', '' )
				.prop( 'checked', false )
				.prop( 'disabled', false );
			$row.find( '.wpforms-field-map-custom-value' )
				.attr( 'name', '' )
				.attr( 'type', 'text' )
				.prop( 'readonly', false )
				.val( '' );
		},

		/**
		 * On form save notify the user about "Required fields".
		 *
		 * @since 1.0.0
		 *
		 * @type {object}
		 */
		requiredFields: {

			/**
			 * True if we have not filled required fields.
			 *
			 * @since 1.0.0
			 *
			 * @type {boolean}
			 */
			hasErrors: false,

			/**
			 * We need to notify the user only once.
			 *
			 * @since 1.0.0
			 *
			 * @type {boolean}
			 */
			isNotified: false,

			/**
			 * Initialization for required fields checking.
			 *
			 * @since 1.0.0
			 */
			init: function() {

				var $settingBlocks = app.$holder.find( '.wpforms-builder-settings-block-webhook' );

				if (
					! $settingBlocks.length ||
					$settingBlocks.hasClass( 'hidden' )
				) {
					return;
				}

				app.requiredFields.isNotified = false;
				$settingBlocks.each( app.requiredFields.check );
			},

			/**
			 * Do the actual required fields check.
			 *
			 * @since 1.0.0
			 */
			check: function() {

				app.requiredFields.hasErrors = false;

				$( this ).find( 'input.wpforms-required, select.wpforms-required' ).each( function() {

					var $field = $( this ),
						value  = $field.val();

					if (
						_.isEmpty( value ) ||
						( $field.hasClass( 'wpforms-required-url' ) && ! wpf.isURL( value ) )
					) {
						$field.addClass( 'wpforms-error' );
						app.requiredFields.hasErrors = true;

					} else {
						$field.removeClass( 'wpforms-error' );
					}
				} );

				// Notify user.
				app.requiredFields.notify();
			},

			/**
			 * Modal that use for user notification.
			 *
			 * @since 1.0.0
			 */
			notify: function() {

				if ( app.requiredFields.hasErrors && ! app.requiredFields.isNotified ) {
					$.alert( {
						title: wpforms_builder.heads_up,
						content: wpforms_builder.webhook_required_flds,
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

					// Save that we have already showed the user.
					app.requiredFields.isNotified = true;
				}
			},
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

WPFormsBuilderWebhooks.init();
