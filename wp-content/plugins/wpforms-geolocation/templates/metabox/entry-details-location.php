<?php
/**
 * Entry Location metabox.
 *
 * @since 2.0.0
 *
 * @var \WPFormsGeolocation\Admin\Entry $entry   Current entry.
 * @var string                          $map_url Map url.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="wpforms-entry-geolocation" class="postbox">

	<h2 class="hndle"><span><?php esc_html_e( 'Location', 'wpforms-geolocation' ); ?></span></h2>

	<div class="inside">

		<?php if ( empty( $entry->entry_location ) ) { ?>

			<p style="padding:0 10px 10px;"><?php esc_html_e( 'Unable to load location data for this entry. This usually means WPForms was unable to process the user\'s IP address or it is non-standard format.', 'wpforms-geolocation' ); ?></p>

			<?php
		} else {
			if ( ! empty( $map_url ) ) {
				?>
				<iframe frameborder="0" src="<?php echo esc_url( $map_url ); ?>" style="width:100%;height:320px;"></iframe>
			<?php } ?>
			<ul>

				<?php // General location. ?>
				<li>
					<span class="wpforms-geolocation-meta"><?php esc_html_e( 'Location', 'wpforms-geolocation' ); ?></span>
					<?php
					$location = implode(
						', ',
						array_filter(
							[
								! empty( $entry->entry_location['city'] ) ? $entry->entry_location['city'] : '',
								! empty( $entry->entry_location['region'] ) ? $entry->entry_location['region'] : '',
							]
						)
					);
					?>
					<span class="wpforms-geolocation-value"><?php echo esc_html( $location ); ?></span>
				</li>

				<?php // Zipcode/postal. ?>
				<li>
					<?php if ( ! empty( $entry->entry_location['country'] ) && $entry->entry_location['country'] === 'US' ) { ?>
						<span class="wpforms-geolocation-meta"><?php esc_html_e( 'Zipcode', 'wpforms-geolocation' ); ?></span>
					<?php } else { ?>
						<span class="wpforms-geolocation-meta"><?php esc_html_e( 'Postal', 'wpforms-geolocation' ); ?></span>
					<?php } ?>
					<span class="wpforms-geolocation-value"><?php echo esc_html( ! empty( $entry->entry_location['postal'] ) ? $entry->entry_location['postal'] : '' ); ?></span>
				</li>

				<?php // Country. ?>
				<li>
					<span class="wpforms-geolocation-meta"><?php esc_html_e( 'Country', 'wpforms-geolocation' ); ?></span>
					<?php $country = ! empty( $entry->entry_location['country'] ) ? $entry->entry_location['country'] : ''; ?>
					<span class="wpforms-geolocation-value">
						<span class="wpforms-flag<?php echo ! empty( $country ) ? ' wpforms-flag-' . esc_html( strtolower( $country ) ) : ''; ?>"></span>
						<?php echo esc_html( $country ); ?>
					</span>
				</li>

				<?php // Lat/long. ?>
				<li>
					<span class="wpforms-geolocation-meta"><?php esc_html_e( 'Lat/Long', 'wpforms-geolocation' ); ?></span>
					<?php
					$latlong = implode(
						', ',
						array_filter(
							[
								! empty( $entry->entry_location['latitude'] ) ? $entry->entry_location['latitude'] : '',
								! empty( $entry->entry_location['longitude'] ) ? $entry->entry_location['longitude'] : '',
							]
						)
					);
					?>
					<span class="wpforms-geolocation-value"><?php echo esc_html( $latlong ); ?></span>
				</li>

			</ul>

		<?php } ?>

	</div>

</div>
