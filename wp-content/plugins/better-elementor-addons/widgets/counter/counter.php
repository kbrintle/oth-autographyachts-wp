<?php
namespace BetterWidgets\Widgets;

use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Frontend;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


		
/**
 * @since 1.0.0
 */
class Better_Counter extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'better-counter';
	}
	
	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'circle-progress','better-el-addons' ];
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Counter', 'better-el-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-counter bea-widget-badge';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'better-category' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
	
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Button Settings', 'better-el-addons' ),
			]
		);

		$this->add_control(
			'counter_style',
			[
				'label' => __( 'Style', 'better-el-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => __( 'Style 1', 'better-el-addons' ),
					'2' => __( 'Style 2', 'better-el-addons' ),
				],
				'default' => '1',
			]
		);

        $this->add_control(
			'counter_title',
			[
				'label' => __( 'Counter Title','better-el-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label_block' => true,
				'default' => 'Marketing',
			]
		);

		$this->add_control(
			'counter_sub_title',
			[
				'label' => __( 'Counter Sub-Title','better-el-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label_block' => true,
				'default' => 'Marketing',
				'condition' => [
					'counter_style' => '2'
				]
			]
		);

		$this->add_control(
			'counter_percent',
			[
				'label' => __( 'Width', 'better-el-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 50,
				],
			]
		);
		

		$this->end_controls_section();

        $this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Content Style', 'better-el-addons' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'better_counter_bar_color',
			[
				'label' => esc_html__( 'Circle or Bar Color', 'better-el-addons' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#eee'
			]
		);

        $this->add_control(
			'better_counter_percent_color',
			[
				'label' => esc_html__( 'Percent Color', 'better-el-addons' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
					'{{WRAPPER}} .better-counter .skill span' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'better_counter_percent_typography',
				'label' => esc_html__( 'Percent Typography', 'better-el-addons' ),
				'selector' => '{{WRAPPER}} .better-counter .skill span',
			]
		);

        $this->add_control(
			'better_counter_title_color',
			[
				'label' => esc_html__( 'Title Color', 'better-el-addons' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
					'{{WRAPPER}} .better-counter h6' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'better_counter_title_typography',
				'label' => esc_html__( 'Title Typography', 'better-el-addons' ),
				'selector' => '{{WRAPPER}} .better-counter h6',
			]
		);

		$this->add_control(
			'better_counter_sub_title_color',
			[
				'label' => esc_html__( 'Sub Title Color', 'better-el-addons' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
					'{{WRAPPER}} .better-counter span' => 'color: {{VALUE}}',
				],
				'condition' => [
					'counter_style' => '2'
				]
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'better_counter_sub_title_typography',
				'label' => esc_html__( 'Sub Title Typography', 'better-el-addons' ),
				'selector' => '{{WRAPPER}} .better-counter span',
				'condition' => [
					'counter_style' => '2'
				]
			]
		);

        $this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();
		
        // Styles selections.
		$style = $settings['counter_style'];
		$allowed_styles = array('1', '2'); // Add more styles as needed


	    // Check if the selected style is in the allowed list
	    if (in_array($style, $allowed_styles)) {
	        // If the style is allowed, include the corresponding file
	        include( 'styles/style'.$style.'.php' );
	    } else {
	        // If the style is not selected
	        echo "Invalid style selected";
	    }
	
		
	 
		}

}


