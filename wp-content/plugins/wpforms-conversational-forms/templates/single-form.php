<?php
/**
 * The template for displaying single Conversational Form.
 *
 * @since 1.0.0
 */

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="wpforms-conversational-form-loading">

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="wpforms-conversational-form-page">
	<div class="wpforms-conversational-form-wrap">
		<main class="wpforms-conversational-form-main" role="main">

			<?php do_action( 'wpforms_conversational_forms_content_before' ); ?>

			<?php wpforms_display( get_the_ID() ); ?>

			<?php do_action( 'wpforms_conversational_forms_content_after' ); ?>

		</main>
	</div>
	<div class="wpforms-conversational-form-footer">
		<div class="wpforms-conversational-form-footer-wrap">

			<?php do_action( 'wpforms_conversational_forms_footer' ); ?>

		</div>
	</div>
</div>

<?php wp_footer(); ?>

</body>

</html>
