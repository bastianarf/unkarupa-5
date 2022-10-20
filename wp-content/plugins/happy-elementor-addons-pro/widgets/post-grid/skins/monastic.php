<?php
namespace Happy_Addons_Pro\Widget\Skins\Post_Grid;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Skin_Base as Elementor_Skin_Base;

use Happy_Addons_Pro\Traits\Lazy_Query_Builder;
use Happy_Addons_Pro\Traits\Post_Grid_Markup;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Monastic extends Skin_Base {

	/**
	 * Get widget ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'monastic';
	}

	/**
	 * Get widget title
	 *
	 * @return string widget title
	 */
	public function get_title() {
		return __( 'Monastic', 'happy-addons-pro' );
	}

	/**
	 * Update Badge Control
	 */
	protected function badge_controls() {

		$this->add_control(
			'show_badge',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'taxonomy_badge',
			[
				'label' => __( 'Badge Taxonomy', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'category',
				'options' => ha_pro_get_taxonomies(),
				'condition' => [
					$this->get_control_id( 'show_badge' ) => 'yes',
				],
			]
		);

	}

}
