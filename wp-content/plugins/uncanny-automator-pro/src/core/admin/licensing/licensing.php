<?php

namespace Uncanny_Automator_Pro;

/**
 * Class AdminMenu
 *
 * This class should only be used to inherit classes
 *
 * @package Uncanny_Automator_Pro
 */
class Licensing {

	/**
	 * The name of the licensing page
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string
	 */
	public $page_name = null;

	/**
	 * The slug of the licensing page
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string
	 */
	public $page_slug = null;

	/**
	 * The slug of the parent that the licensing page is organized under
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string
	 */
	public $parent_slug = null;

	/**
	 * The URL of store powering the plugin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string
	 */
	public $store_url = AUTOMATOR_PRO_STORE_URL;

	/**
	 * The Author of the Plugin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string
	 */
	public $item_name = AUTOMATOR_PRO_ITEM_NAME;

	/**
	 * The Author of the Plugin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string
	 */
	public $author = 'Uncanny Owl';

	/**
	 * Is this a beta version release
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string
	 */
	public $beta = null;

	/**
	 * @var bool|string|null
	 */
	public $error = null;

	/**
	 * Licensing constructor.
	 */
	public function __construct() {
		include __DIR__ . '/EDD_SL_Plugin_Updater.php';

		// Create sub-page for EDD licensing
		$this->page_name   = __( 'Licensing', 'uncanny-automator-pro' );
		$this->page_slug   = 'uncanny-automator-license-activation';
		$this->parent_slug = 'uo-recipe';
		$this->store_url   = AUTOMATOR_PRO_STORE_URL;
		$this->item_name   = AUTOMATOR_PRO_ITEM_NAME;
		$this->author      = 'Uncanny Owl';

		$this->error = $this->set_defaults();

		if ( true !== $this->error ) {

			// Create an admin notices with the error
			add_action( 'admin_notices', array( $this, 'licensing_setup_error' ) );

		} else {

			add_action( 'admin_init', array( $this, 'plugin_updater' ), 0 );
			add_action( 'admin_menu', array( $this, 'license_menu' ), 199 );
			add_action( 'admin_init', array( $this, 'activate_license' ) );
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'uapro_notify_admin_of_license_expiry', array( $this, 'admin_notices_for_expiry' ) );
			add_action( 'admin_notices', array( $this, 'show_expiry_notice' ) );
			add_action( 'admin_notices', array( $this, 'uapro_remind_to_add_license_notice_func' ) );

			//Add license notice
			add_action( 'after_plugin_row', array(
				$this,
				'plugin_row',
			), 10, 3 );
		}
	}


	/**
	 * @param $plugin_name
	 * @param $plugin_data
	 * @param $status
	 */
	public function plugin_row( $plugin_name, $plugin_data, $status ) {
		if ( $plugin_name !== 'uncanny-automator-pro/uncanny-automator-pro.php' ) {
			return;
		}
		$slug    = 'uncanny-automator-pro';
		$message = $this->expiry_message();

		if ( empty( $message ) ) {
			return;
		}
		if ( is_network_admin() ) {
			$active_class = is_plugin_active_for_network( $plugin_name ) ? ' active' : '';
		} else {
			$active_class = is_plugin_active( $plugin_name ) ? ' active' : '';
		}

		// Get the columns for this table so we can calculate the colspan attribute.
		$screen  = get_current_screen();
		$columns = get_column_headers( $screen );

		// If something went wrong with retrieving the columns, default to 3 for colspan.
		$colspan = ! is_countable( $columns ) ? 3 : count( $columns );

		echo '<tr class="plugin-update-tr' . $active_class . '" id="' . $slug . '-update" data-slug="' . $slug . '" data-plugin="' . $plugin_name . '">';
		echo '<td colspan="' . $colspan . '" class="plugin-update colspanchange">';
		echo '<div class="update-message notice inline notice-warning notice-alt">';
		echo '<p>';
		echo $message;
		echo '</p></div></td></tr>';

		// Apply the class "update" to the plugin row to get rid of the ugly border.
		echo "
				<script type='text/javascript'>
					jQuery('#$slug-update').prev('tr').addClass('update');
				</script>
				";
	}

	/**
	 * @return string
	 */
	public function expiry_message() {
		$this->check_license();
		$license_key    = trim( get_option( 'uap_automator_pro_license_key', '' ) );
		$license_status = get_option( 'uap_automator_pro_license_status', '' );
		$license_expiry = get_option( 'uap_automator_pro_license_expiry' );
		$message        = '';
		$days_diff      = 0;
		if ( ! empty( $license_expiry ) ) {
			$days_diff = round( ( time() - strtotime( $license_expiry ) ) / ( 60 * 60 * 24 ) );
		}
		$renew_link = sprintf(
			'<a href="%s" target="_blank">%s</a>',
			AUTOMATOR_PRO_STORE_URL . 'checkout/?edd_license_key=' . $license_key . '&download_id=' . AUTOMATOR_PRO_ITEM_ID . '&utm_medium=uncanny_automator_pro&utm_campaign=plugins_page',
			__( 'Renew now', 'uncanny-automator-pro' )
		);
		if ( 'expired' === $license_status ) {
			if ( $days_diff >= 1 && $days_diff <= 30 ) {
				$message .= sprintf(
					_x(
						'Your %s license expired on %s. %s to continue to receive updates, support and unlimited usage of third-party integrations.',
						'License expiry notice',
						'uncanny-automator-pro'
					),
					'<strong>Uncanny Automator Pro</strong>',
					date( 'F d, Y', strtotime( $license_expiry ) ),
					$renew_link
				);
			} elseif ( $days_diff > 30 ) {
				$message .= sprintf(
					_x(
						'Your %s license expired more than 30 days ago. %s. Check the %s under "%s" to see which ones. %s to continue running these recipes.',
						'License expiry notice',
						'uncanny-automator-pro'
					),
					'<strong>Uncanny Automator Pro</strong>',
					'<strong>' . __( 'Some of your recipes are no longer running', 'uncanny-automator-pro' ) . '</strong>',
					sprintf(
						'<a href="%s">%s</a>',
						admin_url( 'edit.php?post_type=uo-recipe&page=uncanny-automator-dashboard' ),
						__( 'Uncanny Automator dashboard', 'uncanny-automator-pro' )
					),
					__( 'Recipes using credits', 'uncanny-automator' ),
					$renew_link
				);
			} else {
				$message .= sprintf(
					_x(
						'Your license for %s has expired. %s to continue to receive updates, support and unlimited usage of third-party integrations.',
						'License expiry notice',
						'uncanny-automator-pro'
					),
					'<strong>Uncanny Automator Pro</strong>',
					$renew_link
				);
			}
		} elseif ( empty( $license_key ) || ( 'valid' !== $license_status && 'expired' !== $license_status ) ) {
			$message .= sprintf(
				__( "%s your copy of %s to get access to automatic updates, support and unlimited usage of third-party integrations. Don't have a license key? Click %s to buy one.", 'uncanny-automator-pro' ),
				sprintf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=uo-recipe&page=uncanny-automator-license-activation' ), __( 'Activate', 'uncanny-automator-pro' ) ),
				'<strong>Uncanny Automator Pro</strong>',
				sprintf( '<a href="%s" target="_blank">%s</a>', 'https://automatorplugin.com/pricing/?utm_medium=uncanny_automator_pro&utm_campaign=license_page#pricing', __( 'here', 'uncanny-automator-pro' ) )
			);
		}

		return $message;
	}

	/**
	 *
	 */
	public function uapro_remind_to_add_license_notice_func() {
		$license_key    = trim( get_option( 'uap_automator_pro_license_key', '' ) );
		$license_status = get_option( 'uap_automator_pro_license_status', '' );
		if ( filter_has_var( INPUT_GET, 'page' ) && 'uncanny-automator-license-activation' === filter_input( INPUT_GET, 'page' ) ) {
			return;
		}
		if ( ! empty( $license_key ) && ( 'valid' !== $license_status || 'expired' !== $license_status ) ) {
			return;
		}
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php
				printf(
					__( "%s your copy of %s to get access to automatic updates, support and unlimited usage of third-party integrations. Don't have a license key? Click %s to buy one.", 'uncanny-automator-pro' ),
					sprintf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=uo-recipe&page=uncanny-automator-license-activation' ), __( 'Activate', 'uncanny-automator-pro' ) ),
					'<strong>Uncanny Automator Pro</strong>',
					sprintf( '<a href="%s" target="_blank">%s</a>', 'https://automatorplugin.com/pricing/?utm_medium=uncanny_automator_pro&utm_campaign=admin_header#pricing', __( 'here', 'uncanny-automator-pro' ) )
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Add Licensing menu and sub-page
	 *
	 * @since 1.0.0
	 */
	public function license_menu() {
		add_submenu_page(
			'edit.php?post_type=uo-recipe',
			/* translators: 1. Trademarked term */
			sprintf( __( '%1$s license', 'uncanny-automator-pro' ), 'Uncanny Automator Pro' ),
			__( 'Automator Pro license', 'uncanny-automator-pro' ),
			'manage_options',
			'uncanny-automator-license-activation',
			array(
				$this,
				'license_page',
			)
		);
	}

	/**
	 *
	 */
	public function admin_notices_for_expiry() {
		$license_data = $this->check_license( true );
	}

	/**
	 *
	 */
	public function show_expiry_notice() {
		$status = get_option( 'uap_automator_pro_license_status', '' );
		if ( filter_has_var( INPUT_GET, 'page' ) && 'uncanny-automator-license-activation' === filter_input( INPUT_GET, 'page' ) ) {
			return;
		}
		if ( empty( $status ) ) {
			return;
		}
		if ( 'expired' !== $status ) {
			return;
		}
		?>
		<div class="notice notice-error <?php if ( ! $this->is_automator_page() ) { ?>is-dismissible<?php } ?>">
			<p>
				<?php
				echo $this->expiry_message();
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * @return bool
	 */
	public function is_automator_page() {
		if ( filter_has_var( INPUT_GET, 'post_type' ) && preg_match( '/uo\-recipe/', filter_input( INPUT_GET, 'post_type' ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Set all the defaults for the plugin licensing
	 *
	 * @return bool|string True if success and error message if not
	 * @since    1.0.0
	 * @access   private
	 *
	 */
	private function set_defaults() {

		if ( null === $this->page_name ) {
			$this->page_name = AUTOMATOR_PRO_ITEM_NAME;
		}

		if ( null === $this->page_slug ) {
			$this->page_slug = 'uncanny-automator-license-activation';
		}

		if ( null === $this->parent_slug ) {
			$this->parent_slug = 'uo-recipe';
		}

		if ( null === $this->store_url ) {
			return __( 'Error: Licensed plugin store URL not set.', 'uncanny-automator-pro' );
		}

		if ( null === $this->item_name ) {
			return __( 'Error: Licensed plugin item name not set', 'uncanny-automator-pro' );
		}

		if ( null === $this->author ) {
			$this->author = 'Uncanny Owl';
		}

		if ( null === $this->beta ) {
			$this->beta = false;
		}

		return true;

	}

	/**
	 * Admin Notice to notify that the needed licencing variables have not been set
	 *
	 * @since    1.0.0
	 */
	public function licensing_setup_error() {

		?>
		<div class="notice notice-error is-dismissible">
			<p><?php printf( __( 'There may be an issue with the configuration of %s.', 'uncanny-automator-pro' ), 'Uncanny Automator Pro' ); ?>
				<br><?php echo $this->error; ?></p>
		</div>
		<?php

	}

	/**
	 * Calls the EDD SL Class
	 *
	 * @since    1.0.0
	 */
	public function plugin_updater() {

		// retrieve our license key from the DB
		$license_key = trim( get_option( 'uap_automator_pro_license_key' ) );

		// setup the updater
		new EDD_SL_Plugin_Updater(
			AUTOMATOR_PRO_STORE_URL,
			AUTOMATOR_PRO_FILE,
			array(
				'version'   => AUTOMATOR_PRO_PLUGIN_VERSION,
				'license'   => $license_key,
				'item_name' => AUTOMATOR_PRO_ITEM_NAME,
				'author'    => 'Uncanny Owl',
				'beta'      => $this->beta,
			)
		);

	}

	/**
	 * Sub-page out put
	 *
	 * @since    1.0.0
	 */
	public function license_page() {

		$license_data = $this->check_license( true );

		$license = get_option( 'uap_automator_pro_license_key' );
		$status  = get_option( 'uap_automator_pro_license_status' ); // $license_data->license will be either "valid", "invalid", "expired", "disabled"

		// Check license status
		$license_is_active = ( 'valid' === $status ) ? true : false;

		// CSS Classes
		$license_css_classes = array();

		if ( $license_is_active ) {
			$license_css_classes[] = 'uo-license--active';
		}

		// Set links. Add UTM parameters at the end of each URL
		$where_to_get_my_license = 'https://automatorplugin.com/knowledge-base/where-can-i-find-my-license-key/?utm_source=uncanny_automator_pro&utm_medium=license_page&utm_content=where_to_get_my_license';
		$buy_new_license         = 'https://automatorplugin.com/pricing/?utm_source=uncanny_automator_pro&utm_medium=license_page&utm_content=buy_new_license';
		$knowledge_base          = 'https://automatorplugin.com/knowledge-base/?utm_source=uncanny_automator_pro&utm_medium=license_page&utm_content=knowledge_base';

		include __DIR__ . '/admin-license.php';
	}

	/**
	 * API call to activate License
	 *
	 * @since    1.0.0
	 */
	public function activate_license() {

		// listen for our activate button to be clicked
		if ( ! filter_has_var( INPUT_POST, 'uapro_license_activate' ) ) {
			return;
		}

		// run a quick security check
		if ( ! check_admin_referer( 'uapro_nonce', 'uapro_nonce' ) ) {
			return;
		} // get out if we didn't click the Activate button

		// Save license key
		$license = sanitize_text_field( trim( filter_input( INPUT_POST, 'uap_automator_pro_license_key' ) ) );
		update_option( 'uap_automator_pro_license_key', $license );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ), // the name of our product in uo
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post( $this->store_url, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		) );

		// make sure the response came back okay
		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'uncanny-automator-pro' );
			}

		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired' :
						$message = sprintf(
							__( 'Your license key expired on %s.', 'uncanny-automator-pro' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
						$message = __( 'Your license key has been disabled.', 'uncanny-automator-pro' );
						break;
					case 'missing' :
						$message = __( 'Invalid license.', 'uncanny-automator-pro' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.', 'uncanny-automator-pro' );
						break;
					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'uncanny-automator-pro' ), $this->item_name );
						break;
					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'uncanny-automator-pro' );
						break;
					default :
						$message = __( 'An error occurred, please try again.', 'uncanny-automator-pro' );
						break;
				}
			}
		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'edit.php?post_type=uo-recipe&page=' . $this->page_slug );
			$redirect = add_query_arg( array(
				'sl_activation' => 'false',
				'message'       => urlencode( $message ),
			), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"
		update_option( 'uap_automator_pro_license_status', $license_data->license );
		update_option( 'uap_automator_pro_license_expiry', $license_data->expires );
		wp_redirect( admin_url( 'edit.php?post_type=uo-recipe&page=' . $this->page_slug ) );
		exit();
	}

	/**
	 * API call to de-activate License
	 *
	 * @since    1.0.0
	 */
	public function deactivate_license() {

		// listen for our activate button to be clicked
		if ( ! filter_has_var( INPUT_POST, 'uapro_license_deactivate' ) ) {
			return;
		}

		// run a quick security check
		if ( ! check_admin_referer( 'uapro_nonce', 'uapro_nonce' ) ) {
			return;
		} // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'uap_automator_pro_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ), // the name of our product in uo
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post( $this->store_url, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'uncanny-automator-pro' );
			}

			$base_url = admin_url( 'edit.php?post_type=uo-recipe&page=' . $this->page_slug );
			$redirect = add_query_arg( array(
				'sl_activation' => 'false',
				'message'       => urlencode( $message ),
			), $base_url );

			wp_redirect( $redirect );

			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data->license == 'deactivated' ) {
			delete_option( 'uap_automator_pro_license_status' );
			delete_option( 'uap_automator_pro_license_expiry' );
		}

		wp_redirect( admin_url( 'edit.php?post_type=uo-recipe&page=' . $this->page_slug ) );
		exit();
	}


	/**
	 * Load Scripts that are specific to the admin page
	 *
	 * @param string $hook Admin page being loaded
	 *
	 * @since 1.0
	 *
	 */
	public function admin_scripts( $hook ) {

		if ( 'uo-recipe_page_uncanny-automator-license-activation' === $hook ) {
			wp_enqueue_style( 'uapro-admin-license', Utilities::get_css( 'admin/license.css' ), array(), AUTOMATOR_PRO_PLUGIN_VERSION );
		}
	}


	/**
	 * This is a means of catching errors from the activation method above and displaying it to the customer
	 *
	 * @since    1.0.0
	 */
	public function admin_notices() {

		if ( filter_has_var( INPUT_GET, 'page' ) && $this->page_slug == filter_input( INPUT_GET, 'page' ) ) {

			if ( filter_has_var( INPUT_GET, 'sl_activation' ) && ! empty( filter_input( INPUT_GET, 'message' ) ) ) {

				switch ( filter_input( INPUT_GET, 'sl_activation' ) ) {

					case 'false':

						$message = urldecode( esc_html__( wp_kses( filter_input( INPUT_GET, 'message' ), array() ), 'uncanny-automator-pro' ) );

						?>
						<div class="notice notice-error">
							<p><?php echo $message; ?></p>
						</div>
						<?php

						break;

					case 'true':
					default:
						?>
						<div class="notice notice-success">
							<p><?php _e( 'License is activated.', 'uncanny-automator-pro' ); ?></p>
						</div>
						<?php
						break;

				}
			}
		}
	}

	/**
	 * API call to check if License key is valid
	 *
	 * The updater class does this for you. This function can be used to do something custom.
	 *
	 * @return null|object|bool
	 * @since    1.0.0
	 * @throws \Exception
	 */
	public function check_license( $force_check = false ) {
		$last_checked = get_option( 'uap_automator_pro_license_last_checked' );
		if ( ! empty( $last_checked ) && false === $force_check ) {
			$datediff = time() - $last_checked;
			if ( $datediff < DAY_IN_SECONDS ) {
				return null;
			}
		}
		if ( true === $force_check ) {
			delete_option( 'uap_automator_pro_license_last_checked' );
		}
		$license = trim( get_option( 'uap_automator_pro_license_key' ) );
		if ( empty( $license ) ) {
			return new \stdClass();
		}
		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post( $this->store_url, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// this license is still valid
		if ( $license_data->license == 'valid' ) {
			update_option( 'uap_automator_pro_license_status', $license_data->license );
			if ( 'lifetime' !== $license_data->expires ) {
				update_option( 'uap_automator_pro_license_expiry', $license_data->expires );
			} else {
				update_option( 'uap_automator_pro_license_expiry', date( 'Y-m-d H:i:s', mktime( 12, 59, 59, 12, 31, 2099 ) ) );
			}

			if ( 'lifetime' !== $license_data->expires ) {
				$expire_notification = new \DateTime( $license_data->expires, wp_timezone() );
				update_option( 'uap_automator_pro_license_expiry_notice', $expire_notification );
				if ( wp_get_scheduled_event( 'uapro_notify_admin_of_license_expiry' ) ) {
					wp_unschedule_hook( 'uapro_notify_admin_of_license_expiry' );
				}
				// 1 hour after the license is schedule to expire.
				wp_schedule_single_event( $expire_notification->getTimestamp() + 3600, 'uapro_notify_admin_of_license_expiry' );

			}
		} else {
			update_option( 'uap_automator_pro_license_status', 'invalid' );
			update_option( 'uap_automator_pro_license_expiry', '' );
			// this license is no longer valid
		}
		update_option( 'uap_automator_pro_license_last_checked', time() );

		return $license_data;
	}

}
