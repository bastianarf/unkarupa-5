<?php
/**
 * Add Happy Particle Effects to Section and Column
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Extension;

use Elementor\Controls_Manager;

defined('ABSPATH') || die();

class Particle_Effects {

	public static function init() {
		// Enqueue scripts.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'elementor/element/after_section_end', array( __CLASS__, 'register_controls' ), 10, 3 );

		add_action( 'elementor/section/print_template', array( __CLASS__, '_print_template' ), 10, 2 );
		add_action( 'elementor/column/print_template', array( __CLASS__, '_print_template' ), 10, 2 );

		add_action( 'elementor/frontend/column/before_render', array( __CLASS__, '_before_render' ), 10, 1 );
		add_action( 'elementor/frontend/section/before_render', array( __CLASS__, '_before_render' ), 10, 1 );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers all the scripts defined as extension dependencies and enqueues them.
	 *
	 * @access public
	 */
	public static function enqueue_scripts() {

		if ( ( true === \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) || ( function_exists( 'elementor_location_exits' ) && ( elementor_location_exits( 'archive', true ) || elementor_location_exits( 'single', true ) ) ) ) {
					wp_add_inline_script(
						'elementor-frontend',
						'window.scope_array = [];

								window.backend = 0;

								jQuery.cachedScript = function( url, options ) {
									// Allow user to set any option except for dataType, cache, and url.
									options = jQuery.extend( options || {}, {
										dataType: "script",
										cache: true,
										url: url
									});
									// Return the jqXHR object so we can chain callbacks happy.
									return jQuery.ajax( options );
								};

							    jQuery( window ).on( "elementor/frontend/init", function() {
									elementorFrontend.hooks.addAction( "frontend/element_ready/global", function( $scope, $ ){
										if ( "undefined" == typeof $scope ) {
												return;
										}
										if ( $scope.hasClass( "ha-particle-yes" ) ) {
											window.scope_array.push( $scope );
											$scope.find(".ha-particle-wrapper").addClass("js-is-enabled");
										}else{
											return;
										}
										if(elementorFrontend.isEditMode() && $scope.find(".ha-particle-wrapper").hasClass("js-is-enabled") && window.backend == 0 ){
											var ha_url = HappyProLocalize.ha_particles_url;
											console.log( ha_url );

											jQuery.cachedScript( ha_url );
											window.backend = 1;
										}else if(elementorFrontend.isEditMode()){
											var ha_url = HappyProLocalize.ha_particles_url;
											console.log( ha_url );

											jQuery.cachedScript( ha_url ).done(function(){
												var flag = true;
											});
										}
									});
								});

								jQuery(document).ready(function(){
									if ( jQuery.find( ".ha-particle-yes" ).length < 1 ) {
										return;
									}
									var ha_url = HappyProLocalize.ha_particles_url;
									console.log( ha_url );
									jQuery.cachedScript = function( url, options ) {
										// Allow user to set any option except for dataType, cache, and url.
										options = jQuery.extend( options || {}, {
											dataType: "script",
											cache: true,
											url: url
										});
										// Return the jqXHR object so we can chain callbacks.
										return jQuery.ajax( options );
									};
									jQuery.cachedScript( ha_url );
								});	'
					);
		}
	}

	/**
	 * Register Particle Backgrounds controls.
	 *
	 * @access public
	 * @param object $element for current element.
	 * @param object $section_id for section ID.
	 * @param array  $args for section args.
	 */
	public static function register_controls( $element, $section_id, $args ) {

		if ( ( 'section' === $element->get_name() && 'section_background' === $section_id ) || ( 'column' === $element->get_name() && 'section_style' === $section_id ) ) {

			$element->start_controls_section(
				'ha_particles',
				array(
					'tab'   => Controls_Manager::TAB_STYLE,
					'label' => __( 'Happy Particle Effects', 'happy-addons-pro' ),
				)
			);

			$element->add_control(
				'ha_enable_particles',
				array(
					'type'         => Controls_Manager::SWITCHER,
					'label'        => __( 'Enable Particle Background', 'happy-addons-pro' ),
					'default'      => '',
					'label_on'     => __( 'Yes', 'happy-addons-pro' ),
					'label_off'    => __( 'No', 'happy-addons-pro' ),
					'return_value' => 'yes',
					'prefix_class' => 'ha-particle-',
					'render_type'  => 'template',
				)
			);

			/* $element->add_control(
				'ha_particles_default_css',
				array(
					'label' => __( 'Hidden Style', 'plugin-domain' ),
					'type' => \Elementor\Controls_Manager::HIDDEN,
					'default'      => 'width: 100%;height: 100%;position: absolute;left: 0;top: 0;',
					'selectors' => [
						'{{WRAPPER}} .ha-particle-wrapper' => '{{VALUE}}',
					],
					'condition' => array(
						'ha_enable_particles' => 'yes',
					),
				)
			); */

			$element->add_control(
				'ha_particles_styles',
				array(
					'label'     => __( 'Style', 'happy-addons-pro' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'nasa',
					'options'   => array(
						'default' => __( 'Polygon', 'happy-addons-pro' ),
						'nasa'    => __( 'NASA', 'happy-addons-pro' ),
						'snow'    => __( 'Snow', 'happy-addons-pro' ),
						'custom'  => __( 'Custom', 'happy-addons-pro' ),
					),
					'condition' => array(
						'ha_enable_particles' => 'yes',
					),
				)
			);

			$element->add_control(
				'help_doc_particles_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'Add custom JSON for the Particle Background below. To generate a completely customized background style follow steps below - ', 'happy-addons-pro' ),
					'content_classes' => 'ha-editor-doc ha-editor-description',
					'condition'       => array(
						'ha_enable_particles' => 'yes',
						'ha_particles_styles' => 'custom',
					),
				)
			);

			$element->add_control(
				'help_doc_particles_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( __( '1. Visit a link %1$s here %2$s and choose required attributes for particle </br></br> 2. Once a custom style is created, download JSON from "Download current config (json)" link </br></br> 3. Copy JSON code from the downloaded file and paste it below', 'happy-addons-pro' ), '<a href="https://vincentgarreau.com/particles.js/" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'ha-editor-doc ha-editor-description',
					'condition'       => array(
						'ha_enable_particles' => 'yes',
						'ha_particles_styles' => 'custom',
					),
				)
			);

			/* translators: %s admin link */
			/* translators: %s admin link */
			/* translators: %s admin link */
			/* $element->add_control(
				'help_doc_particles_5',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( __( 'To know more about creating a custom style, refer to a document %1$s here %2$s.', 'happy-addons-pro' ), '<a href=https://happyaddon.com" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'ha-editor-doc ha-editor-description',
					'condition'       => array(
						'ha_enable_particles' => 'yes',
						'ha_particles_styles' => 'custom',
					),
				)
			); */

			$element->add_control(
				'ha_particle_json',
				array(
					'type'        => Controls_Manager::CODE,
					'default'     => '',
					'render_type' => 'template',
					'condition'   => array(
						'ha_enable_particles' => 'yes',
						'ha_particles_styles' => 'custom',
					),
				)
			);

			$element->add_control(
				'ha_particles_color',
				array(
					'label'       => __( 'Particle Color', 'happy-addons-pro' ),
					'type'        => Controls_Manager::COLOR,
					'alpha'       => false,
					'condition'   => array(
						'ha_enable_particles'  => 'yes',
						'ha_particles_styles!' => 'custom',
					),
					'render_type' => 'template',
				)
			);

			$element->add_control(
				'ha_particles_opacity',
				array(
					'label'       => __( 'Opacity', 'happy-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1,
							'step' => 0.1,
						),
					),
					'condition'   => array(
						'ha_enable_particles'  => 'yes',
						'ha_particles_styles!' => 'custom',
					),
					'render_type' => 'template',
				)
			);

			$element->add_control(
				'ha_particles_direction',
				array(
					'label'     => __( 'Flow Direction', 'happy-addons-pro' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'bottom',
					'options'   => array(
						'top'          => __( 'Top', 'happy-addons-pro' ),
						'bottom'       => __( 'Bottom', 'happy-addons-pro' ),
						'left'         => __( 'Left', 'happy-addons-pro' ),
						'right'        => __( 'Right', 'happy-addons-pro' ),
						'top-left'     => __( 'Top Left', 'happy-addons-pro' ),
						'top-right'    => __( 'Top Right', 'happy-addons-pro' ),
						'bottom-left'  => __( 'Bottom Left', 'happy-addons-pro' ),
						'bottom-right' => __( 'Bottom Right', 'happy-addons-pro' ),
					),
					'condition' => array(
						'ha_enable_particles' => 'yes',
						'ha_particles_styles' => 'snow',
					),
				)
			);

			$element->add_control(
				'ha_enable_advanced',
				array(
					'type'         => Controls_Manager::SWITCHER,
					'label'        => __( 'Advanced Settings', 'happy-addons-pro' ),
					'default'      => 'no',
					'label_on'     => __( 'Yes', 'happy-addons-pro' ),
					'label_off'    => __( 'No', 'happy-addons-pro' ),
					'return_value' => 'yes',
					'prefix_class' => 'ha-particle-adv-',
					'render_type'  => 'template',
					'condition'    => array(
						'ha_enable_particles'  => 'yes',
						'ha_particles_styles!' => 'custom',
					),
				)
			);

			$element->add_control(
				'ha_particles_number',
				array(
					'label'       => __( 'Number of Particles', 'happy-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'min' => 1,
							'max' => 500,
						),
					),
					'condition'   => array(
						'ha_enable_particles'  => 'yes',
						'ha_particles_styles!' => 'custom',
						'ha_enable_advanced'   => 'yes',
					),
					'render_type' => 'template',
				)
			);

			$element->add_control(
				'ha_particles_size',
				array(
					'label'       => __( 'Particle Size', 'happy-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'min' => 1,
							'max' => 200,
						),
					),
					'condition'   => array(
						'ha_enable_particles'  => 'yes',
						'ha_particles_styles!' => 'custom',
						'ha_enable_advanced'   => 'yes',
					),
					'render_type' => 'template',
				)
			);

			$element->add_control(
				'ha_particles_speed',
				array(
					'label'       => __( 'Move Speed', 'happy-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'min' => 1,
							'max' => 10,
						),
					),
					'condition'   => array(
						'ha_enable_particles'  => 'yes',
						'ha_particles_styles!' => 'custom',
						'ha_enable_advanced'   => 'yes',
					),
					'render_type' => 'template',
				)
			);

			$element->add_control(
				'ha_enable_interactive',
				array(
					'type'         => Controls_Manager::SWITCHER,
					'label'        => __( 'Enable Hover Effect', 'happy-addons-pro' ),
					'default'      => 'no',
					'label_on'     => __( 'Yes', 'happy-addons-pro' ),
					'label_off'    => __( 'No', 'happy-addons-pro' ),
					'return_value' => 'yes',
					'condition'    => array(
						'ha_enable_particles'  => 'yes',
						'ha_particles_styles!' => 'custom',
						'ha_enable_advanced'   => 'yes',
					),
					'render_type'  => 'template',
				)
			);

			$element->add_control(
				'help_doc_interactive',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'Particle hover effect will not work in the following scenarios - </br></br> 1. In the Elementor backend editor</br></br> 2. Content/Spacer added in the section/column occupies the entire space and leaves it inaccessible. Adding padding to the section/column can resolve this.', 'happy-addons-pro' ),
					'content_classes' => 'ha-editor-doc',
					'condition'       => array(
						'ha_enable_particles'   => 'yes',
						'ha_particles_styles!'  => 'custom',
						'ha_enable_advanced'    => 'yes',
						'ha_enable_interactive' => 'yes',
					),
				)
			);


			/* $element->add_control(
				'help_doc_interactive_not_working',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( __( 'Learn more about this %1$s here. %2$s', 'happy-addons-pro' ), '<a href=https://happyaddons.com" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'ha-editor-doc',
					'condition'       => array(
						'ha_enable_particles'   => 'yes',
						'ha_particles_styles!'  => 'custom',
						'ha_enable_advanced'    => 'yes',
						'ha_enable_interactive' => 'yes',
					),
				)
			); */

			$element->end_controls_section();
		}
	}

	/**
	 * Render Particles Background output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access public
	 * @param object $element for current element.
	 */
	public static function _before_render( $element ) {

		if ( $element->get_name() !== 'section' && $element->get_name() !== 'column' ) {
			return;
		}

		$settings  = $element->get_settings();
		$node_id   = $element->get_id();
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		if ( 'yes' === $settings['ha_enable_particles'] ) {
			$element->add_render_attribute( '_wrapper', 'data-ha-partstyle', $settings['ha_particles_styles'] );
			$element->add_render_attribute( '_wrapper', 'data-ha-partcolor', $settings['ha_particles_color'] );
			$element->add_render_attribute( '_wrapper', 'data-ha-partopacity', $settings['ha_particles_opacity']['size'] );
			$element->add_render_attribute( '_wrapper', 'data-ha-partdirection', $settings['ha_particles_direction'] );

			if ( 'yes' === $settings['ha_enable_advanced'] ) {
				$element->add_render_attribute( '_wrapper', 'data-ha-partnum', $settings['ha_particles_number']['size'] );
				$element->add_render_attribute( '_wrapper', 'data-ha-partsize', $settings['ha_particles_size']['size'] );
				$element->add_render_attribute( '_wrapper', 'data-ha-partspeed', $settings['ha_particles_speed']['size'] );
				if ( $is_editor ) {
					$element->add_render_attribute( '_wrapper', 'data-ha-interactive', 'no' );
				} else {
					$element->add_render_attribute( '_wrapper', 'data-ha-interactive', $settings['ha_enable_interactive'] );
				}
			}

			if ( 'custom' === $settings['ha_particles_styles'] ) {
				$element->add_render_attribute( '_wrapper', 'data-ha-partdata', $settings['ha_particle_json'] );
			}
		}
	}

	/**
	 * Render Particles Background output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access public
	 * @param object $template for current template.
	 * @param object $widget for current widget.
	 */
	public static function _print_template( $template, $widget ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		if ( $widget->get_name() !== 'section' && $widget->get_name() !== 'column' ) {
			return $template;
		}
		$old_template = $template;
		ob_start();
		?>
		<# if( 'yes' == settings.ha_enable_particles ) {

			view.addRenderAttribute( 'particle_data', 'id', 'ha-particle-' + view.getID() );
			view.addRenderAttribute( 'particle_data', 'class', 'ha-particle-wrapper' );
			view.addRenderAttribute( 'particle_data', 'data-ha-partstyle', settings.ha_particles_styles );
			view.addRenderAttribute( 'particle_data', 'data-ha-partcolor', settings.ha_particles_color );
			view.addRenderAttribute( 'particle_data', 'data-ha-partopacity', settings.ha_particles_opacity.size );
			view.addRenderAttribute( 'particle_data', 'data-ha-partdirection', settings.ha_particles_direction );

			if( 'yes' == settings.ha_enable_advanced ) {
				view.addRenderAttribute( 'particle_data', 'data-ha-partnum', settings.ha_particles_number.size );
				view.addRenderAttribute( 'particle_data', 'data-ha-partsize', settings.ha_particles_size.size );
				view.addRenderAttribute( 'particle_data', 'data-ha-partspeed', settings.ha_particles_speed.size );
				view.addRenderAttribute( 'particle_data', 'data-ha-interactive', 'no' );

			}
			if ( 'custom' == settings.ha_particles_styles ) {
				view.addRenderAttribute( 'particle_data', 'data-ha-partdata', settings.ha_particle_json );
			}
			#>
			<div {{{ view.getRenderAttributeString( 'particle_data' ) }}}></div>
		<# } #>
		<?php
		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;
		return $template;
	}

}

Particle_Effects::init();
