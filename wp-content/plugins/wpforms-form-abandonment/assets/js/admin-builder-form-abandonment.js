;(function($) {

	var WPFormsFormAbandonment = {

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			$(document).ready(WPFormsFormAbandonment.ready);
		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready: function() {

			WPFormsFormAbandonment.conditionals();
		},

		/**
		 * Register and load conditionals.
		 *
		 * @since 1.0.0
		 */
		conditionals: function() {

			$( '#wpforms-panel-field-settings-form_abandonment' ).conditions( {
				conditions: {
					element: '#wpforms-panel-field-settings-form_abandonment',
					type:    'checked',
					operator:'is'
				},
				actions: {
					if: {
						element: '#wpforms-panel-field-settings-form_abandonment_fields-wrap,#wpforms-panel-field-settings-form_abandonment_duplicates-wrap',
						action: 'show'
					},
					else: {
						element: '#wpforms-panel-field-settings-form_abandonment_fields-wrap,#wpforms-panel-field-settings-form_abandonment_duplicates-wrap',
						action:  'hide'
					}
				},
				effect: 'appear'
			} );
		}
	};

	WPFormsFormAbandonment.init();
})(jQuery);