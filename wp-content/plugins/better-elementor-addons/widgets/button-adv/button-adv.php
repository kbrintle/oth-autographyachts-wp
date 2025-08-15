<?php
namespace BetterWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;


if (!defined('ABSPATH')) exit; // Exit if accessed directly



/**
 * @since 1.0.0
 */
class Better_Button_adv extends Widget_Base
{

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'better-button-adv';
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
	public function get_title()
	{
		return __('Button Advanced', 'BEA');
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
	public function get_icon()
	{
		return 'eicon-button bea-widget-badge';
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
	public function get_categories()
	{
		return [ 'better-category' ];
	}

	 //script depend
	public function get_script_depends() { 
        return ['button-adv']; 
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
	protected function register_controls()
	{
		//----------------------------------------------- Box content section-----------------------------------//


		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Button Settings', 'BEA'),
			]
		);

		$this->add_control(
			'btn_text',
			[
				'label' => __('Button Text', 'BEA'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => 'Click now',

			]
		);

		$this->add_control(
			'link',
			[
				'label' => __('Button Link', 'BEA'),
				'type' => Controls_Manager::URL,
				'placeholder' => 'Leave Link here',
			]
		);
		$this->add_control(
			'button_alignment',
			[
				'label' => __('Button Text Alignment', 'BEA'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'BEA'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'BEA'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'BEA'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'elementor-align-',
				'default' => 'left',
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => esc_html__('Icon', 'BEA'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => esc_html__('Icon Position', 'BEA'),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => esc_html__('Before', 'BEA'),
					'right' => esc_html__('After', 'BEA'),
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label' => esc_html__('Icon Spacing', 'BEA'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button .bea-adv-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bea-adv-button .bea-adv-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		//---------------------------------------- Floating icon hover style section---------------------------------------//

		$this->start_controls_section(
			'section-second-text',
			[
				'label' => __('Second Text Animation', 'BEA'),
			]
		);
		
		$this->add_control(
			'show_text_animations',
			[
				'label' => esc_html__( 'Second Text Animation', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'on', 'BEA' ),
				'label_off' => esc_html__( 'off', 'BEA' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
        $this->add_control(
			'btn_second_text',
			[
				'label' => __('Second Text', 'BEA'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => 'Show More',
				'condition' => [
					'show_text_animations' => 'yes',
				],
			]
		);
		$this->end_controls_section();

		//---------------------------------------- button style section---------------------------------------//


        $this->start_controls_section(
            'button-style-section',
            [
                'label' => esc_html__( 'Button', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'button_text_typography',
				'selector' => '{{WRAPPER}} .bea-adv-button .bea-adv-button-text',
			]
		);
        $this->start_controls_tabs(
            'style_tabs-button'
        );
		//------------------------ button normal style section------------------------//
        
        $this->start_controls_tab(
            'button_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );
        $this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'BEA' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button .bea-adv-button-text' => 'color: {{VALUE}}',
				],
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'button_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bea-adv-button',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bea-adv-button',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'button-border',
				'selector' => '{{WRAPPER}} .bea-adv-button',
				'separator' => 'before',

			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'border_gradient',
				'label' => __('Border Gradient Color', 'BEA'),
				'types' => [ 'gradient' ],
				'separator' => 'after',
				'selector' => '{{WRAPPER}} .bea-adv-button',
				'exclude' => ['image'],
				'fields_options' => [
					'gradient_type' => [
						'default' => 'radial',
						'type' => Controls_Manager::HIDDEN,
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'background-color: none;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-image-source: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
						],
						'type' => Controls_Manager::HIDDEN,
					],
					'gradient_position' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-image-slice: 1; border-image-source: radial-gradient(circle farthest-corner at 10% 20%, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
						],
					],
				],
			]
		);
      
        $this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'BEA' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->add_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'BEA' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->end_controls_tab();
		//----------------------------- button hover style section---------------------//
        $this->start_controls_tab(
            'button_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );
        $this->add_control(
			'text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'BEA' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button:hover .bea-adv-button-text' => 'color: {{VALUE}}',
				],
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'button_background_hover',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bea-adv-button:hover',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .bea-adv-button:hover',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'button-border-hover',
				'selector' => '{{WRAPPER}} .bea-adv-button:hover',
				'separator' => 'before'

			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'border_gradient_hover',
				'label' => __('Border Gradient Color', 'BEA'),
				'types' => [ 'gradient' ],
				'separator' => 'after',
				'selector' => '{{WRAPPER}} .bea-adv-button:hover',
				'exclude' => ['image'],
				'fields_options' => [
					'gradient_type' => [
						'default' => 'radial',
						'type' => Controls_Manager::HIDDEN,
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'background-color: none;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-image-source: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
						],
						'type' => Controls_Manager::HIDDEN,
					],
					'gradient_position' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-image-slice: 1; border-image-source: radial-gradient(circle farthest-corner at 10% 20%, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
						],
					],
				],
			]
		);
      
        $this->add_control(
			'button_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'BEA' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->add_control(
			'button_padding_hover',
			[
				'label' => esc_html__( 'Padding', 'BEA' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

		//--------------------------------------- icon hover style section---------------------------------------//
        $this->start_controls_section(
            'button-icon-style-section',
            [
                'label' => esc_html__( 'Button Icon', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
       
        $this->start_controls_tabs(
            'icon_tabs'
        );
		//----------------------------- icon normal style section---------------------//
        
        $this->start_controls_tab(
            'icon_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );
        $this->add_responsive_control(
			'icon-size',
			[
				'label' => esc_html__( 'icon Size', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 18,
				],
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bea-adv-button-icon svg ' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        $this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'BEA' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button-icon i ' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bea-adv-button-icon svg path ' => 'fill: {{VALUE}}',
				],
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'icon-background',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bea-adv-button-icon',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'icon-border',
				'selector' => '{{WRAPPER}} .bea-adv-button-icon',
			]
		);
        $this->add_control(
			'icon_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'BEA' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}  .bea-adv-button-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->add_control(
			'icon_padding',
			[
				'label' => esc_html__( 'Padding', 'BEA' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}  .bea-adv-button-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->end_controls_tab();
		//----------------------------- button hover style section---------------------//

        $this->start_controls_tab(
            'icon_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );
        $this->add_responsive_control(
			'icon-size_hover',
			[
				'label' => esc_html__( 'icon Size', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button:hover .bea-adv-button-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bea-adv-button:hover .bea-adv-button-icon svg ' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        $this->add_control(
			'icon_color_hover',
			[
				'label' => esc_html__( 'Color', 'BEA' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button:hover .bea-adv-button-icon i ' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bea-adv-button:hover .bea-adv-button-icon svg path ' => 'fill: {{VALUE}}',
				],
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'icon-background_hover',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bea-adv-button:hover .bea-adv-button-icon',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'icon-border_hover',
				'selector' => '{{WRAPPER}} .bea-adv-button:hover .bea-adv-button-icon',
			]
		);
        $this->add_control(
			'icon_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'BEA' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button:hover  .bea-adv-button-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->add_control(
			'icon_padding_hover',
			[
				'label' => esc_html__( 'Padding', 'BEA' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button:hover  .bea-adv-button-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
		$this->end_controls_section();

		//--------------------------------------- button animation style section---------------------------------------//
        $this->start_controls_section(
            'animation-section',
            [
                'label' => esc_html__( 'Animation', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'animation_style',
			[
				'label' => esc_html__( 'Animation', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'Default', 'BEA' ),
					'bg' => esc_html__( 'Background', 'BEA' ),
					'border'  => esc_html__( 'Border', 'BEA' ),
					'mouse-interactive'  => esc_html__( 'Mouse Interaction', 'BEA' ),
					'move-with-cursor'  => esc_html__( 'Move With Cursor', 'BEA' ),
					'icon-bg'  => esc_html__( 'Icon Background', 'BEA' ),
				],
			]
		);
		$this->add_control(
			'border_animantion_type',
			[
				'label' => esc_html__( 'Type', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'corners-out',
				'options' => [
					'corners-in' => esc_html__( 'Corners In', 'BEA' ),
					'corners-out' => esc_html__( 'Corners Out', 'BEA' ),
					'sequance' => esc_html__( 'Sequance', 'BEA' ),
				],
				'condition' => [
					'animation_style' => 'border',
				],
			]
		);

		$this->add_control(
			'border_animantion_color',
			[
				'label' => esc_html__( 'Color', 'BEA' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bea-adv-border-top  , {{WRAPPER}} .bea-adv-border-bottom , {{WRAPPER}} .bea-adv-border-left , {{WRAPPER}} .bea-adv-border-right' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'animation_style' => 'border',
				],
			]
		);
		$this->add_control(
			'border_animantion_height',
			[
				'label' => esc_html__( 'border Weight', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} .bea-adv-border-top ,{{WRAPPER}}  .bea-adv-border-bottom' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bea-adv-border-left ,{{WRAPPER}} .bea-adv-border-right' => 'width: {{SIZE}}{{UNIT}};',		
				],
				'condition' => [
					'animation_style' => 'border',
				],
			]
		);

		$this->add_control(
			'bg_animantion_type',
			[
				'label' => esc_html__( 'Type', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'slide-left',
				'options' => [
					'slide-left' => esc_html__( 'Slide Left', 'BEA' ),
					'slide-right' => esc_html__( 'Slide right', 'BEA' ),
					'slide-bottom' => esc_html__( 'Slide bottom', 'BEA' ),
					'slide-top' => esc_html__( 'Slide top', 'BEA' ),
				],
				'condition' => [
					'animation_style' => 'bg',
				],
			]
		);
		$this->add_control(
			'bg_animantion_start',
			[
				'label' => esc_html__( 'start Weight', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .bea-animated-bg-slide-top::before ,{{WRAPPER}}  .bea-animated-bg-slide-bottom::before' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bea-animated-bg-slide-left::before ,{{WRAPPER}} .bea-animated-bg-slide-right::before' => 'width: {{SIZE}}{{UNIT}};',		
				],
				'condition' => [
					'animation_style' => 'bg',
				],
			]
		);
		$this->add_control(
			'bg_animantion_color',
			[
				'label' => esc_html__( 'Color', 'BEA' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bea-adv-button::before ,{{WRAPPER}} .bea-adv-button-circle,{{WRAPPER}} .bea-animated-icon-bg::after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'animation_style' => ['bg','mouse-interactive','icon-bg'],
				],
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
	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$animation = 'none';
		$second_text=" ";

		if($settings['animation_style']=="border"){
			$animation = $settings['animation_style'].'-' . $settings['border_animantion_type'];
		} else if($settings['animation_style']=="bg"){
			$animation = $settings['animation_style'].'-' . $settings['bg_animantion_type'];
		} else if($settings['animation_style']=="mouse-interactive"){
			$animation = $settings['animation_style'];
		} else if($settings['animation_style']=="move-with-cursor"){
			$animation = 'btn-cursor';
		}else if($settings['animation_style']=="icon-bg"){
			$animation = $settings['animation_style'];
		}

		if($settings['show_text_animations']=="yes"){
			$second_text = 'bea-second-text';
		}

		$this->add_render_attribute([
			'button' => [
				'class' => [
					'bea-adv-button',
					'bea-animated-' . $animation
				],
			],
			'content-wrapper' => [
				'class' => ['bea-adv-button-content-wrapper'],
			],
			'icon-align' => [
				'class' => [
					'bea-adv-button-icon',
					'bea-adv-align-icon-' . $settings['icon_align'],
				],
			],
			'btn_text' => [
				'class' => [
					'bea-adv-button-text',
					$second_text
				],
			],
		]);

		$this->add_inline_editing_attributes('btn_text', 'none');
        ?>

		<a href="<?php echo esc_url($settings['link']['url']); ?>" <?php if ( $settings['link']['is_external'] ) {echo'target="_blank"';} ?> <?php $this->print_render_attribute_string('button'); ?>>
			<?php if($settings['animation_style']=="mouse-interactive"){ ?><span class="bea-adv-button-circle desplode-circle"></span><?php } ?>

			<?php if($settings['animation_style']=="border"){ ?>
				<div class="bea-border-lines <?php echo esc_attr($settings['border_animantion_type']);?>" >
					<div class="bea-adv-border-top"></div>
					<div class="bea-adv-border-bottom"></div>
					<div class="bea-adv-border-left"></div>
					<div class="bea-adv-border-right"></div>
				</div>
			<?php } ?>
			<span <?php $this->print_render_attribute_string('content-wrapper'); ?>>
				<?php if (!empty($settings['selected_icon']['value']) and $settings['icon_align'] == 'left') : ?>
					<span <?php $this->print_render_attribute_string('icon-align'); ?>>
						<?php Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);?>
					</span>
				<?php endif; ?>
				<div <?php $this->print_render_attribute_string('btn_text');  if($settings['show_text_animations']=="yes"){echo 'data-text="'.esc_attr($settings['btn_second_text']).'"';} ?>>
					<span><?php echo esc_html($settings['btn_text']); ?></span>
				</div>
				<?php if (!empty($settings['selected_icon']['value'])  and $settings['icon_align'] == 'right') : ?>
					<span <?php $this->print_render_attribute_string('icon-align'); ?>>
                    <?php Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);?>
					</span>
				<?php endif; ?>
			</span>
		</a>

        <?php
	}

}

