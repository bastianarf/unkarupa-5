<?php
/**
 * The template for Conversational Form dynamically generated color scheme.
 *
 * @since 1.0.0
 *
 * @var $color string Hex color picked by user.
 */

$color_main      = $color;
$color_secondary = $this->colors->hex_lighten( $color_main, 0.3 );
$color_medium    = $this->colors->hex_to_rgba_css_value( $color_main, 0.5 );
$color_light     = $this->colors->hex_to_rgba_css_value( $color_main, 0.1 );

$color_page_bg = \wpforms_light_or_dark( $color_main, $this->colors->hex_darken( $color_main, 0.6 ), $this->colors->hex_lighten( $color_main, 0.6 ) );

$color_element_bg_light = $this->colors->hex_to_rgba_css_value( $color_main, 0.025 );
$color_element_bg_hover = $this->colors->hex_to_rgba_css_value( $color_main, 0.05 );

$color_text_contrast         = \wpforms_light_or_dark( $color_main, $this->colors->hex_darken( $color_main, 0.6 ), '#ffffff' );
$color_text_contrast_opacity = \wpforms_light_or_dark( $color_main, $this->colors->hex_darken( $color_main, 0.6 ), 'rgba(255, 255, 255, 0.8)' );

$color_btn_hover  = \wpforms_light_or_dark( $color_main, $this->colors->hex_darken( $color_main, 0.1 ), $this->colors->hex_lighten( $color_main, 0.2 ) );
$color_btn_active = \wpforms_light_or_dark( $color_main, $this->colors->hex_darken( $color_main, 0.14 ), $this->colors->hex_lighten( $color_main, 0.17 ) );

$color_checkbox_bg = \wpforms_light_or_dark( $color_main, $this->colors->hex_opacity( $color_page_bg, 0.96 ), $this->colors->hex_opacity( $color_page_bg, 0.55 ) );

?>

<style type="text/css">

	/* =========================================
	  Animation
	-------------------------------------------- */

	@-webkit-keyframes selected-item-blink {
		0% {
			background-color: <?php echo \esc_attr( $color_main ); ?>;
		}
		49% {
			background-color: <?php echo \esc_attr( $color_main ); ?>;
		}
		50% {
			background-color: transparent;
		}
		99% {
			background-color: transparent;
		}
		100% {
			background-color: <?php echo \esc_attr( $color_main ); ?>;
		}
	}

	@-moz-keyframes selected-item-blink {
		0% {
			background-color: <?php echo \esc_attr( $color_main ); ?>;
		}
		49% {
			background-color: <?php echo \esc_attr( $color_main ); ?>;
		}
		50% {
			background-color: transparent;
		}
		99% {
			background-color: transparent;
		}
		100% {
			background-color: <?php echo \esc_attr( $color_main ); ?>;
		}
	}

	@keyframes selected-item-blink {
		0% {
			background-color: <?php echo \esc_attr( $color_main ); ?>;
		}
		49% {
			background-color: <?php echo \esc_attr( $color_main ); ?>;
		}
		50% {
			background-color: transparent;
		}
		99% {
			background-color: transparent;
		}
		100% {
			background-color: <?php echo \esc_attr( $color_main ); ?>;
		}
	}


	/* =========================================
      Loader
	-------------------------------------------- */

	.wpforms-conversational-form-loading #wpforms-conversational-form-loader-container {
		background-color: <?php echo \esc_attr( $color_page_bg ); ?>;
	}

	.wpforms-conversational-form-loading #wpforms-conversational-form-loader-container .wpforms-conversational-form-loader {
		border-top: 1.1em solid <?php echo \esc_attr( $color_light ); ?>;
		border-right: 1.1em solid <?php echo \esc_attr( $color_light ); ?>;
		border-bottom: 1.1em solid <?php echo \esc_attr( $color_light ); ?>;
		border-left: 1.1em solid <?php echo \esc_attr( $color_main ); ?>;
	}
	.wpforms-conversational-form-loading #wpforms-conversational-form-loader-container .wpforms-conversational-form-loader-powered-by span {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	.wpforms-conversational-form-loading #wpforms-conversational-form-loader-container .wpforms-conversational-form-loader-powered-by .cls-1 {
		fill: <?php echo \esc_attr( $color_main ); ?>;
	}


	/* =========================================
      General page styles
	-------------------------------------------- */

	body {
		background-color: <?php echo \esc_attr( $color_page_bg ); ?>;
	}


	/* =========================================
      General form styles
	-------------------------------------------- */

	#wpforms-conversational-form-page {
		background-color: <?php echo \esc_attr( $color_page_bg ); ?>;
		background: -moz-radial-gradient(center, ellipse cover, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 100%); /* FF3.6-15 */
		background: -webkit-radial-gradient(center, ellipse cover, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 100%); /* Chrome10-25,Safari5.1-6 */
		background: radial-gradient(ellipse at center, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 100%);
		background-attachment: fixed;
	}

	#wpforms-conversational-form-page h1, #wpforms-conversational-form-page h2, #wpforms-conversational-form-page h3, #wpforms-conversational-form-page h4, #wpforms-conversational-form-page h5, #wpforms-conversational-form-page h6 {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	@-webkit-keyframes autofill {
		to {
			color: <?php echo \esc_attr( $color_main ); ?>;
			background: transparent;
		}
	}

	#wpforms-conversational-form-page input:-webkit-autofill {
		-webkit-animation-name: autofill;
		-webkit-animation-fill-mode: both;
	}

	#wpforms-conversational-form-page input[type="button"],
	#wpforms-conversational-form-page input[type="checkbox"],
	#wpforms-conversational-form-page input[type="email"],
	#wpforms-conversational-form-page input[type="file"],
	#wpforms-conversational-form-page input[type="submit"],
	#wpforms-conversational-form-page input[type="tel"],
	#wpforms-conversational-form-page input[type="text"],
	#wpforms-conversational-form-page input[type="password"],
	#wpforms-conversational-form-page input[type="url"],
	#wpforms-conversational-form-page input[type="number"],
	#wpforms-conversational-form-page textarea,
	#wpforms-conversational-form-page select {
		color: <?php echo \esc_attr( $color_main ); ?>;
		border-bottom: 1px solid <?php echo \esc_attr( $color_light ); ?>;
	}

	#wpforms-conversational-form-page ::-webkit-input-placeholder {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page ::-moz-placeholder {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page :-ms-input-placeholder {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page :-moz-placeholder {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page label,
	#wpforms-conversational-form-page span {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-sublabel,
	#wpforms-conversational-form-page label.wpforms-error {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}


	/* --------------------------------
	      Conversational form styles
	   -------------------------------- */

	#wpforms-conversational-form-page .wpforms-conversational-form-wrap {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}


	/* --- Form header --- */

	#wpforms-conversational-form-page .wpforms-title {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-description {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}


	/* --- Button container --- */

	#wpforms-conversational-form-page .wpforms-conversational-btn,
	#wpforms-conversational-form-page .wpforms-confirmation-container .wpforms-conversational-btn,
	#wpforms-conversational-form-page .wpforms-confirmation-container-full .wpforms-conversational-btn,
	#wpforms-conversational-form-page .wpforms-confirmation-container button,
	#wpforms-conversational-form-page .wpforms-confirmation-container-full button {
		background-color: <?php echo \esc_attr( $color_secondary ); ?>;
		color: <?php echo \esc_attr( $color_text_contrast_opacity ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-btn:hover,
	#wpforms-conversational-form-page .wpforms-conversational-btn:focus,
	#wpforms-conversational-form-page .wpforms-confirmation-container .wpforms-conversational-btn:hover,
	#wpforms-conversational-form-page .wpforms-confirmation-container .wpforms-conversational-btn:focus,
	#wpforms-conversational-form-page .wpforms-confirmation-container-full .wpforms-conversational-btn:hover,
	.wpforms-confirmation-container-full .wpforms-conversational-btn:focus,
	#wpforms-conversational-form-page .wpforms-confirmation-container-full button:hover,
	.wpforms-confirmation-container-full button:focus {
		background-color: <?php echo \esc_attr( $color_btn_hover ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-btn:active,
	#wpforms-conversational-form-page .wpforms-confirmation-container .wpforms-conversational-btn:active,
	#wpforms-conversational-form-page .wpforms-confirmation-container-full .wpforms-conversational-btn:active,
	#wpforms-conversational-form-page .wpforms-confirmation-container button:active,
	#wpforms-conversational-form-page .wpforms-confirmation-container-full button:active{
		background-color: <?php echo \esc_attr( $color_btn_active ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-form-btn-desc {
		color: <?php echo \esc_attr( $color_medium ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-form-field-info {
		color: <?php echo \esc_attr( $color_medium ); ?>;
	}


	/* --- Form checkbox and radio fields --- */

	#wpforms-conversational-form-page .wpforms-field-radio li label,
	#wpforms-conversational-form-page .wpforms-field-payment-multiple li label,
	#wpforms-conversational-form-page .wpforms-field-payment-checkbox li label,
	#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li label,
	#wpforms-conversational-form-page .wpforms-field-checkbox li label {
		background-color: <?php echo \esc_attr( $color_element_bg_light ); ?>;
		border-color: <?php echo \esc_attr( $color_light ); ?>;
	}

	@media (min-width: 769px) {
		#wpforms-conversational-form-page .wpforms-field-radio li:hover label,
		#wpforms-conversational-form-page .wpforms-field-radio li.wpforms-field-item-hover label,
		#wpforms-conversational-form-page .wpforms-field-payment-multiple li:hover label,
		#wpforms-conversational-form-page .wpforms-field-payment-multiple li.wpforms-field-item-hover label,
		#wpforms-conversational-form-page .wpforms-field-payment-checkbox li:hover label,
		#wpforms-conversational-form-page .wpforms-field-payment-checkbox li.wpforms-field-item-hover label,
		#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li:hover label,
		#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li.wpforms-field-item-hover label,
		#wpforms-conversational-form-page .wpforms-field-checkbox li:hover label,
		#wpforms-conversational-form-page .wpforms-field-checkbox li.wpforms-field-item-hover label {
			background-color: <?php echo \esc_attr( $color_element_bg_hover ); ?>;
		}
	}

	#wpforms-conversational-form-page .wpforms-field-radio li label:before,
	#wpforms-conversational-form-page .wpforms-field-payment-multiple li label:before,
	#wpforms-conversational-form-page .wpforms-field-payment-checkbox li label:before,
	#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li label:before,
	#wpforms-conversational-form-page .wpforms-field-checkbox li label:before,
	#wpforms-conversational-form-page .wpforms-image-choices-label:before,
	#wpforms-conversational-form-page .wpforms-field-likert_scale tbody label:after {
		border: 1px solid <?php echo \esc_attr( $color_medium ); ?>;
		color: <?php echo \esc_attr( $color_main ); ?>;
	}


	#wpforms-conversational-form-page .wpforms-field-radio li.wpforms-selected label,
	#wpforms-conversational-form-page .wpforms-field-payment-multiple li.wpforms-selected label,
	#wpforms-conversational-form-page .wpforms-field-payment-checkbox li.wpforms-selected label,
	#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li.wpforms-selected label,
	#wpforms-conversational-form-page .wpforms-field-checkbox li.wpforms-selected label {
		border-color: <?php echo \esc_attr( $color_medium ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-radio li.wpforms-selected label:before,
	#wpforms-conversational-form-page .wpforms-field-radio li.wpforms-selected label .wpforms-image-choices-label:before,
	#wpforms-conversational-form-page .wpforms-field-payment-multiple li.wpforms-selected label:before,
	#wpforms-conversational-form-page .wpforms-field-payment-multiple li.wpforms-selected label .wpforms-image-choices-label:before,
	#wpforms-conversational-form-page .wpforms-field-payment-checkbox li.wpforms-selected label:before,
	#wpforms-conversational-form-page .wpforms-field-payment-checkbox li.wpforms-selected label .wpforms-image-choices-label:before,
	#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li.wpforms-selected label:before,
	#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li.wpforms-selected label .wpforms-image-choices-label:before,
	#wpforms-conversational-form-page .wpforms-field-checkbox li.wpforms-selected label:before,
	#wpforms-conversational-form-page .wpforms-field-checkbox li.wpforms-selected label .wpforms-image-choices-label:before {
		border: 1px solid <?php echo \esc_attr( $color_main ); ?>;
		background-color: <?php echo \esc_attr( $color_main ); ?>;
		color: <?php echo \esc_attr( $color_text_contrast_opacity ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-radio li:not(.wpforms-selected) label:hover:before, #wpforms-conversational-form-page .wpforms-field-radio li:not(.wpforms-selected) label.wpforms-field-item-hover:before,
	#wpforms-conversational-form-page .wpforms-field-payment-multiple li:not(.wpforms-selected) label:hover:before,
	#wpforms-conversational-form-page .wpforms-field-payment-multiple li:not(.wpforms-selected) label.wpforms-field-item-hover:before,
	#wpforms-conversational-form-page .wpforms-field-payment-checkbox li:not(.wpforms-selected) label:hover:before,
	#wpforms-conversational-form-page .wpforms-field-payment-checkbox li:not(.wpforms-selected) label.wpforms-field-item-hover:before,
	#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li:not(.wpforms-selected) label:hover:before,
	#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li:not(.wpforms-selected) label.wpforms-field-item-hover:before,
	#wpforms-conversational-form-page .wpforms-field-checkbox li:not(.wpforms-selected) label:hover:before,
	#wpforms-conversational-form-page .wpforms-field-checkbox li:not(.wpforms-selected) label.wpforms-field-item-hover:before {
		border: 1px solid <?php echo \esc_attr( $color_medium ); ?>;
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	@media (min-width: 769px) {
		#wpforms-conversational-form-page .wpforms-field-radio li:not(.wpforms-selected) label:hover:before, #wpforms-conversational-form-page .wpforms-field-radio li:not(.wpforms-selected) label.wpforms-field-item-hover:before,
		#wpforms-conversational-form-page .wpforms-field-payment-multiple li:not(.wpforms-selected) label:hover:before,
		#wpforms-conversational-form-page .wpforms-field-payment-multiple li:not(.wpforms-selected) label.wpforms-field-item-hover:before,
		#wpforms-conversational-form-page .wpforms-field-payment-checkbox li:not(.wpforms-selected) label:hover:before,
		#wpforms-conversational-form-page .wpforms-field-payment-checkbox li:not(.wpforms-selected) label.wpforms-field-item-hover:before,
		#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li:not(.wpforms-selected) label:hover:before,
		#wpforms-conversational-form-page .wpforms-field-gdpr-checkbox li:not(.wpforms-selected) label.wpforms-field-item-hover:before,
		#wpforms-conversational-form-page .wpforms-field-checkbox li:not(.wpforms-selected) label:hover:before,
		#wpforms-conversational-form-page .wpforms-field-checkbox li:not(.wpforms-selected) label.wpforms-field-item-hover:before {
			border: 1px solid <?php echo \esc_attr( $color_main ); ?>;
			background-color: <?php echo \esc_attr( $color_checkbox_bg ); ?>;
		}
	}


	/* --- Form rating --- */


	#wpforms-conversational-form-page .wpforms-field-rating svg {
		fill: <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-rating-item:after {
		color: <?php echo \esc_attr( $color_medium ); ?>;
	}


	/* --- Form liker --- */

	#wpforms-conversational-form-page .wpforms-field-likert_scale tbody tr th, #wpforms-conversational-form-page .wpforms-field-likert_scale tbody tr td {
		border-bottom: 1px solid <?php echo \esc_attr( $color_light ); ?>;
		border-top: 1px solid <?php echo \esc_attr( $color_light ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-likert_scale tbody label:hover:after, #wpforms-conversational-form-page .wpforms-field-likert_scale tbody label.wpforms-field-item-hover:after {
		background-color: <?php echo \esc_attr( $color_element_bg_light ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-likert_scale input[type="radio"]:checked + label:after,
	#wpforms-conversational-form-page .wpforms-field-likert_scale input[type="checkbox"]:checked + label:after {
		background-color: <?php echo \esc_attr( $color_main ); ?>;
		color: <?php echo \esc_attr( $color_text_contrast_opacity ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-likert_scale input[type="radio"]:checked + label:hover:after, #wpforms-conversational-form-page .wpforms-field-likert_scale input[type="radio"]:checked + label.wpforms-field-item-hover:after,
	#wpforms-conversational-form-page .wpforms-field-likert_scale input[type="checkbox"]:checked + label:hover:after,
	#wpforms-conversational-form-page .wpforms-field-likert_scale input[type="checkbox"]:checked + label.wpforms-field-item-hover:after {
		background-color: <?php echo \esc_attr( $color_main ); ?>;
		color: <?php echo \esc_attr( $color_text_contrast_opacity ); ?>;
	}


	/* --- Form net promoter score --- */

	#wpforms-conversational-form-page .wpforms-field-net_promoter_score table tbody tr td {
		background-color: <?php echo \esc_attr( $color_element_bg_light ); ?>;
		border-color: <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-net_promoter_score table tbody tr td:first-of-type {
		border-left: 1px solid <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-net_promoter_score table tbody tr td label {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-net_promoter_score table tbody tr td label:hover, #wpforms-conversational-form-page .wpforms-field-net_promoter_score table tbody tr td label.wpforms-field-item-hover {
		background-color: <?php echo \esc_attr( $color_element_bg_hover ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-field-net_promoter_score table tbody tr td input[type=radio]:checked + label {
		background: <?php echo \esc_attr( $color_main ); ?>;
		color: <?php echo \esc_attr( $color_text_contrast_opacity ); ?>;
	}


	/* --- Form dropdown --- */

	#wpforms-conversational-form-page .wpforms-conversational-form-dropdown-input input {
		background-color: <?php echo \esc_attr( $color_element_bg_light ); ?>;
		border-color: <?php echo \esc_attr( $color_light ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-form-dropdown-input .fa-chevron-down {
		color: <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-form-dropdown-list.opened {
		border-bottom: 2px dashed <?php echo \esc_attr( $color_main ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-form-dropdown-item {
		background-color: <?php echo \esc_attr( $color_element_bg_light ); ?>;
		border: 1px solid <?php echo \esc_attr( $color_light ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-form-dropdown-item.selected, #wpforms-conversational-form-page .wpforms-conversational-form-dropdown-item:hover, #wpforms-conversational-form-page .wpforms-conversational-form-dropdown-item.wpforms-field-item-hover {
		background-color: <?php echo \esc_attr( $color_element_bg_hover ); ?>;
	}


	/* --- Form submit --- */

	#wpforms-conversational-form-page .wpforms-submit-container {
		border-top: 1px solid <?php echo \esc_attr( $color_light ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-submit {
		background-color: <?php echo \esc_attr( $color_secondary ); ?>;
		color: <?php echo \esc_attr( $color_text_contrast_opacity ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-submit:hover,
	#wpforms-conversational-form-page .wpforms-submit:focus {
		background-color: <?php echo \esc_attr( $color_btn_hover ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-submit:active {
		background-color: <?php echo \esc_attr( $color_btn_active ); ?>;
	}


	/* --- Form footer --- */

	#wpforms-conversational-form-page .wpforms-conversational-form-footer {
		background-color: <?php echo \esc_attr( $color_main ); ?>;
		color: <?php echo \esc_attr( $color_text_contrast ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-form-footer-progress-completed {
		background-color: <?php echo \esc_attr( $color_secondary ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-form-footer-switch-step-up, #wpforms-conversational-form-page .wpforms-conversational-form-footer-switch-step-down {
		border: 2px solid <?php echo \esc_attr( $color_text_contrast ); ?>;
		color: <?php echo \esc_attr( $color_text_contrast ); ?>;
	}

	#wpforms-conversational-form-page .wpforms-conversational-form-footer .cls-1 {
		fill: <?php echo \esc_attr( $color_text_contrast ); ?>;
	}


	/* =========================================
      Form styles for light Theme main color
	-------------------------------------------- */

	<?php if ( 'light' === \wpforms_light_or_dark( $color_main, 'light' ) ) : ?>

		#wpforms-conversational-form-page {
			background: none;
		}

		#wpforms-conversational-form-page .wpforms-conversational-form-footer-progress-completed {
			background-color: <?php echo \esc_attr( $color_medium ); ?>;
		}

		#wpforms-conversational-form-page .wpforms-submit,
		#wpforms-conversational-form-page .wpforms-conversational-btn,
		#wpforms-conversational-form-page button,
		#wpforms-conversational-form-page .wpforms-confirmation-container .wpforms-conversational-btn,
		#wpforms-conversational-form-page .wpforms-confirmation-container-full .wpforms-conversational-btn,
		#wpforms-conversational-form-page .wpforms-confirmation-container button,
		#wpforms-conversational-form-page .wpforms-confirmation-container-full button{
			background-color: <?php echo \esc_attr( $color_main ); ?>;
		}

	<?php endif; ?>

</style>

<?php
