<?php

namespace Uncanny_Automator_Pro;

/**
 * Class Activity_Log_Settings
 *
 * @package Uncanny_Automator_Pro
 */
class Activity_Log_Settings {

	/**
	 * @var string
	 */
	public static $cron_schedule = 'uapro_auto_purge_logs';

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_filter( 'uap_settings_tabs', array( $this, 'add_purge_settings' ) );
		// Adds new button to settings tab.
		add_filter( 'automator_after_settings_extra_buttons', array( $this, 'add_unschedule_btn' ), 10, 3 );
		add_filter( 'automator_settings_header', array( $this, 'automator_settings_header' ), 10, 3 );
		add_filter( 'automator_content_header_css_class', array( $this, 'automator_content_header_css_class' ), 10, 3 );

		add_action( self::$cron_schedule, array( $this, 'delete_old_logs' ) );
		add_action( 'admin_init', array( $this, 'unschedule_prune_logs' ) );
		add_action( 'admin_init', array( $this, 'add_unpruned_notice' ) );
		add_action( 'admin_init', array( $this, 'maybe_schedule_purge_logs' ) );
	}

	/**
	 *
	 */
	public function add_unpruned_notice() {
		if ( ! automator_filter_has_var( 'post_type' ) ) {
			return;
		}

		if ( ! automator_filter_has_var( 'page' ) ) {
			return;
		}

		if ( ! automator_filter_has_var( 'unscheduled' ) ) {
			return;
		}

		if ( 'uo-recipe' !== automator_filter_input( 'post_type' ) ) {
			return;
		}

		if ( 'uncanny-automator-settings' !== automator_filter_input( 'page' ) ) {
			return;
		}
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Activity logs pruning unscheduled.', 'uncanny-automator-pro' ); ?></p>
		</div>
		<?php
	}

	/**
	 *
	 */
	public function unschedule_prune_logs() {
		if ( ! automator_filter_has_var( 'post_type' ) ) {
			return;
		}

		if ( ! automator_filter_has_var( 'page' ) ) {
			return;
		}

		if ( ! automator_filter_has_var( 'unschedule_prune' ) ) {
			return;
		}

		if ( 'uo-recipe' !== automator_filter_input( 'post_type' ) ) {
			return;
		}

		if ( 'uncanny-automator-settings' !== automator_filter_input( 'page' ) ) {
			return;
		}
		as_unschedule_all_actions( self::$cron_schedule );
		$referrer = admin_url( 'edit.php?post_type=uo-recipe&page=uncanny-automator-settings&unscheduled=1' );
		wp_safe_redirect( $referrer . '&pruned=1' );
		exit;
	}

	/**
	 * @param $content
	 * @param $active
	 * @param $tab
	 *
	 * @return mixed|void
	 */
	public function automator_settings_header( $content, $active, $tab ) {
		if ( empty( $tab ) ) {
			return $content;
		}

		if ( ! isset( $tab->settings_field ) || 'uap_automator_settings' !== $tab->settings_field ) {
			return $content;
		}

		$schedule = as_next_scheduled_action( self::$cron_schedule );
		if ( empty( $schedule ) ) {
			/* translators: Sentence to show if auto-pruning is enabled */
			return esc_html__( 'Auto-pruning is not active.', 'uncanny-automator-pro' );
		}
		$purge_days_limit = get_option( 'uap_automator_purge_days' );

		/* translators: Sentence to show if auto-pruning is enabled */

		return esc_html( sprintf( esc_html__( 'Auto-pruning is active. Log entries older than %d days will be deleted daily.', 'uncanny-automator-pro' ), $purge_days_limit ) );
	}

	/**
	 * @param $content
	 * @param $active
	 * @param $tab
	 *
	 * @return mixed|void
	 */
	public function automator_content_header_css_class( $content, $active, $tab ) {
		if ( empty( $tab ) ) {
			return $content;
		}

		if ( ! isset( $tab->settings_field ) || 'uap_automator_settings' !== $tab->settings_field ) {
			return $content;
		}

		$schedule = as_next_scheduled_action( self::$cron_schedule );
		if ( empty( $schedule ) ) {
			return ' uo-setting--inactive';
		}

		return ' uo-setting--active';
	}

	/**
	 * @param $content
	 * @param $active
	 * @param $tab
	 *
	 * @return false|mixed|string
	 */
	public function add_unschedule_btn( $content, $active, $tab ) {
		if ( empty( $tab ) ) {
			return $content;
		}

		if ( ! isset( $tab->settings_field ) || 'uap_automator_settings' !== $tab->settings_field ) {
			return $content;
		}

		$schedule = as_next_scheduled_action( self::$cron_schedule );
		if ( empty( $schedule ) ) {
			return $content;
		}

		ob_start();
		?>
		<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=uo-recipe&page=uncanny-automator-settings&unschedule_prune=1' ) ); ?>"
		   class="uo-settings-btn uo-settings-btn--secondary unschedle-prune-setting-btn">
			<?php esc_html_e( 'Turn off auto-pruning', 'uncanny-automator-pro' ); ?>
		</a>

		<?php
		return ob_get_clean();
	}

	/**
	 *
	 * Add values to settings tab
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function add_purge_settings( $tabs ) {
		$tabs['settings'] = array(
			'name'           => esc_html__( 'Settings', 'uncanny-automator-pro' ),
			'title'          => esc_html__( 'Auto-prune activity logs', 'uncanny-automator-pro' ),
			'description'    => sprintf( '%s <em>%s</em>.', esc_html__( 'Enter a number of days below to activate automatic daily deletion of recipe log entries older than the specified number of days. Logs will only be deleted for recipes that are not', 'uncanny-automator-pro' ), esc_html__( 'In Progress', 'uncanny-automator-pro' ) ),
			'is_pro'         => true,
			'settings_field' => 'uap_automator_settings',
			'wp_nonce_field' => 'uap_automator_nonce',
			'save_btn_name'  => 'uap_automator_purgedays_save',
			'save_btn_title' => esc_html__( 'Schedule', 'uncanny-automator-pro' ),
			'fields'         => array(
				'uap_automator_purge_days' => array(
					'title'           => esc_html__( 'Enter value in days', 'uncanny-automator-pro' ),
					'type'            => 'number',
					'css_classes'     => '',
					'name'            => 'uap_automator_purge_days',
					'placeholder'     => '10',
					'default'         => '10',
					'required'        => true,
					'custom_atts'     => array(
						'min'  => 0,
						'max'  => 365,
						'step' => 1,
					),
					'validation_func' => '',
				),
			),
		);

		return $tabs;
	}

	/**
	 * Delete old logs.
	 */
	public function delete_old_logs() {

		$purge_days_limit = get_option( 'uap_automator_purge_days' );
		if ( empty( $purge_days_limit ) ) {
			return;
		}
		if ( intval( $purge_days_limit ) < 1 ) {
			return;
		}

		global $wpdb;

		$previous_time = gmdate( 'Y-m-d', strtotime( '-' . $purge_days_limit . ' days' ) );
		$recipes       = $wpdb->get_results( $wpdb->prepare( "SELECT `ID`, `automator_recipe_id` FROM {$wpdb->prefix}uap_recipe_log WHERE `date_time` < %s AND ( `completed` = %d OR `completed` = %d  OR `completed` = %d )", $previous_time, 1, 2, 9 ) );

		if ( empty( $recipes ) ) {
			return;
		}

		foreach ( $recipes as $recipe ) {
			$recipe_id               = absint( $recipe->automator_recipe_id );
			$automator_recipe_log_id = absint( $recipe->ID );

			// Purge recipe logs.
			if ( function_exists( 'automator_purge_recipe_logs' ) ) {
				automator_purge_recipe_logs( $recipe_id, $automator_recipe_log_id );
			} else {
				self::automator_purge_recipe_logs( $recipe_id, $automator_recipe_log_id );
			}

			// Purge trigger logs.
			if ( function_exists( 'automator_purge_trigger_logs' ) ) {
				automator_purge_trigger_logs( $recipe_id, $automator_recipe_log_id );
			} else {
				self::automator_purge_trigger_logs( $recipe_id, $automator_recipe_log_id );
			}

			// Purge action logs.
			if ( function_exists( 'automator_purge_action_logs' ) ) {
				automator_purge_action_logs( $recipe_id, $automator_recipe_log_id );
			} else {
				self::automator_purge_action_logs( $recipe_id, $automator_recipe_log_id );
			}

			// Purge closure logs.
			if ( function_exists( 'automator_purge_closure_logs' ) ) {
				automator_purge_closure_logs( $recipe_id, $automator_recipe_log_id );
			} else {
				self::automator_purge_closure_logs( $recipe_id, $automator_recipe_log_id );
			}
		}
	}

	/**
	 * @param $recipe_id
	 * @param $automator_recipe_log_id
	 */
	public static function automator_purge_recipe_logs( $recipe_id, $automator_recipe_log_id ) {
		global $wpdb;

		// delete from uap_recipe_log
		$wpdb->delete(
			$wpdb->prefix . 'uap_recipe_log',
			array(
				'automator_recipe_id' => $recipe_id,
				'ID'                  => $automator_recipe_log_id,
			)
		);
	}

	/**
	 * @param $recipe_id
	 * @param $automator_recipe_log_id
	 */
	public static function automator_purge_trigger_logs( $recipe_id, $automator_recipe_log_id ) {
		global $wpdb;
		$trigger_tbl      = $wpdb->prefix . 'uap_trigger_log';
		$trigger_meta_tbl = $wpdb->prefix . 'uap_trigger_log_meta';
		self::delete_logs( $trigger_tbl, $trigger_meta_tbl, 'automator_trigger_log_id', $recipe_id, $automator_recipe_log_id );
	}

	/**
	 * @param $recipe_id
	 * @param $automator_recipe_log_id
	 */
	public static function automator_purge_action_logs( $recipe_id, $automator_recipe_log_id ) {
		global $wpdb;
		$action_tbl      = $wpdb->prefix . 'uap_action_log';
		$action_meta_tbl = $wpdb->prefix . 'uap_action_log_meta';
		self::delete_logs( $action_tbl, $action_meta_tbl, 'automator_action_log_id', $recipe_id, $automator_recipe_log_id );
	}

	/**
	 * @param $recipe_id
	 * @param $automator_recipe_log_id
	 */
	public static function automator_purge_closure_logs( $recipe_id, $automator_recipe_log_id ) {
		global $wpdb;
		$closure_tbl      = $wpdb->prefix . 'uap_closure_log';
		$closure_meta_tbl = $wpdb->prefix . 'uap_closure_log_meta';
		self::delete_logs( $closure_tbl, $closure_meta_tbl, 'automator_closure_log_id', $recipe_id, $automator_recipe_log_id );
	}

	/**
	 * @param $tbl
	 * @param $tbl_meta
	 * @param $log_meta_key
	 * @param $recipe_id
	 * @param $automator_recipe_log_id
	 */
	public static function delete_logs( $tbl, $tbl_meta, $log_meta_key, $recipe_id, $automator_recipe_log_id ) {
		global $wpdb;
		$results = $wpdb->get_col( $wpdb->prepare( "SELECT `ID` FROM $tbl WHERE automator_recipe_id=%d AND automator_recipe_log_id=%d", $recipe_id, $automator_recipe_log_id ) ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $results ) {
			foreach ( $results as $automator_log_id ) {
				$wpdb->delete(
					$tbl_meta,
					array( $log_meta_key => $automator_log_id )
				);
			}
		}

		$wpdb->delete(
			$tbl,
			array(
				'automator_recipe_id'     => $recipe_id,
				'automator_recipe_log_id' => $automator_recipe_log_id,
			)
		);
	}

	/**
	 *
	 */
	public static function maybe_schedule_purge_logs() {

		if ( ! automator_filter_has_var( 'uap_automator_nonce', INPUT_POST ) ) {
			return;
		}

		if ( ! wp_verify_nonce( automator_filter_input( 'uap_automator_nonce', INPUT_POST ), 'uap_automator_nonce' ) ) {
			return;
		}

		as_unschedule_all_actions( self::$cron_schedule );

		//Add Action Scheduler event
		as_schedule_cron_action( strtotime( 'midnight tonight' ), '@daily', self::$cron_schedule );
	}
}

