;(function ( $ ) {

	var WPFormsPostSubmissions = {

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function () {

			WPFormsPostSubmissions.bindUIActions();

			$( document ).ready( WPFormsPostSubmissions.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready: function () {

		},

		/**
		 * Element bindings.
		 *
		 * @since 1.0.0
		 */
		bindUIActions: function () {

			// When a featured image field is configured, configure that file
			// upload field to only accept images.
			$( document ).on( 'change', '#wpforms-panel-field-settings-post_submissions_featured', function () {

				var fieldID = $( this ).find( 'option:selected' ).val();

				if ( fieldID !== '' ) {
					$( '#wpforms-field-option-' + fieldID + '-extensions' ).val( 'jpg,jpeg,png,gif' );
					$( '#wpforms-field-option-' + fieldID + '-max_file_number' ).val( 1 );
				}
			} );
		}
	};

	WPFormsPostSubmissions.init();
})( jQuery );
