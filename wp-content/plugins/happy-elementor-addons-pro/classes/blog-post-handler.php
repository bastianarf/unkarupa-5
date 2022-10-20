<?php
namespace Happy_Addons_Pro;

use Elementor\Icons_Manager;
use Happy_Addons_Pro\Traits\Smart_Post_List_Markup;
use Happy_Addons_Pro\Traits\Post_Grid_Markup;
use WP_Query;

defined( 'ABSPATH' ) || die();

/**
 * blog post handler class
 */
class Blog_Post_Handler {

	use Smart_Post_List_Markup;
	use Post_Grid_Markup;

	/**
	 * Initialization
	 *
	 * @return void
	 */
	public static function init() {

		add_action( 'wp_ajax_ha_smart_post_list_action', [ __CLASS__, 'smart_post_list_ajax' ] );
		add_action( 'wp_ajax_nopriv_ha_smart_post_list_action', [ __CLASS__, 'smart_post_list_ajax' ] );

		add_action( 'wp_ajax_hapro_post_grid_action', [ __CLASS__, 'post_grid_ajax' ] );
		add_action( 'wp_ajax_nopriv_hapro_post_grid_action', [ __CLASS__, 'post_grid_ajax' ] );
	}

	/**
	 * Smart Post List Ajax Handler
	 */
	public static function smart_post_list_ajax() {

		$security = check_ajax_referer('happy_addons_pro_nonce', 'security');

		if (true == $security && isset($_POST['querySettings'])) :

			$settings = $_POST['querySettings'];

			$list_column = $settings['list_column'];
			$class_array = [];
			if( 'yes' === $settings['make_featured_post']){
				$class_array['featured'] = 'ha-spl-column ha-spl-featured-post-wrap '.esc_attr($settings['featured_post_column']);
				$class_array['featured_inner'] = 'ha-spl-featured-post '.'ha-spl-featured-'.esc_attr($settings['featured_post_style']);
			}

			$per_page = $settings['per_page'];
			$args = $settings['args'];
			$args['posts_per_page'] = $per_page;

			if( $_POST['offset'] ){
				$args['offset'] = $_POST['offset'];
			}
			if( $_POST['termId'] && is_numeric($_POST['termId']) ){
				$args['tax_query'] = array(
					array(
						'taxonomy' => '',
						'field' => 'term_taxonomy_id',
						'terms' => $_POST['termId'],
					),
				);
			}

			$args['suppress_filters'] = false;

			$posts = get_posts( $args );
			$loop = 1;

			if ( count( $posts ) !== 0 ){

				self::render_spl_markup( $settings, $posts, $class_array, $list_column, $per_page );
			}


		endif;
		wp_die();

	}

	/**
	 * Post Grid Ajax Handler
	 */
	public static function post_grid_ajax() {

		$security = check_ajax_referer('happy_addons_pro_nonce', 'security');

		if (true == $security && isset($_POST['querySettings'])) :

			$settings = $_POST['querySettings'];
			$loaded_item = $_POST['loadedItem'];

			$args = $settings['args'];
			$args['offset'] = $loaded_item;
			$_query = new WP_Query( $args );

			if ( $_query->have_posts() ) :
				while ( $_query->have_posts() ) : $_query->the_post();

					if( 'classic' == $settings['_skin'] ){
						self::render_classic_markup( $settings, $_query );
					}
					elseif( 'hawai' == $settings['_skin'] ){
						self::render_hawai_markup( $settings, $_query );
					}
					elseif( 'standard' == $settings['_skin'] ){
						self::render_standard_markup( $settings, $_query );
					}
					elseif( 'monastic' == $settings['_skin'] ){
						self::render_monastic_markup( $settings, $_query );
					}
					elseif( 'stylica' == $settings['_skin'] ){
						self::render_stylica_markup( $settings, $_query );
					}
					elseif( 'outbox' == $settings['_skin'] ){
						self::render_outbox_markup( $settings, $_query );
					}
					elseif( 'crossroad' == $settings['_skin'] ){
						self::render_crossroad_markup( $settings, $_query );
					}

				endwhile;
				wp_reset_postdata();
			endif;
		endif;
		wp_die();
	}

}

Blog_Post_Handler::init();
