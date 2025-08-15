<?php

namespace BetterWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Utils;


if (!defined('ABSPATH')) exit; // Exit if accessed directly



/**
 * @since 1.0.0
 */
class Better_Icon_Box extends Widget_Base
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
        return 'better-icon-box';
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
        return __('Icon Box', 'BEA');
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
        return 'eicon-icon-box bea-widget-badge';
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
        return ['better-category'];
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

          //----------------------------------------------- Box settings section-----------------------------------//


          $this->start_controls_section(
            'section_settings',
            [
                'label' => __('Settings', 'BEA'),
            ]
        );
        
        $this->add_control(
            'show_Box_Icon',
            [
                'label' => esc_html__( 'Show Box Icon', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'BEA' ),
                'label_off' => esc_html__( 'Hide', 'BEA' ),
                'return_value' => 'yes',
                'default' => 'yes',
                
            ]
        );
        $this->add_control(
            'show_title',
            [
                'label' => esc_html__( 'Show Title', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'BEA' ),
                'label_off' => esc_html__( 'Hide', 'BEA' ),
                'return_value' => 'yes',
                'default' => 'yes',
                
            ]
        );
        $this->add_control(
            'show_description',
            [
                'label' => esc_html__( 'Show Description', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'BEA' ),
                'label_off' => esc_html__( 'Hide', 'BEA' ),
                'return_value' => 'yes',
                'default' => 'yes',
                
            ]
        );
        $this->add_control(
            'Text_alignment',
            [
                'label' => __('Alignment', 'BEA'),
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
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box ' => ' text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'heading_position',
            [
                'label' => esc_html__( 'Title Position', 'BEA' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'above' => [
                        'title' => esc_html__( 'above Icon', 'BEA' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'under' => [
                        'title' => esc_html__( 'Under Icon', 'BEA' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'under',
                'toggle' => true,
            ]
        );
        $this->add_control(
            'animated_description',
            [
                'label' => esc_html__( 'Animated Description', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__( 'None', 'BEA' ),
                    'fadeup'  => esc_html__( 'FadeUp', 'BEA' ),
                ],
            ]
        );
        $this->add_control(
            'bea_heading_tag',
            [
                'label' => esc_html__( 'Title HTML Tag', 'BEA' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
            ]
        );

        $this->end_controls_section();

        //----------------------------------------------- Box content section-----------------------------------//


        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content ', 'BEA'),
            ]
        );
        $this->add_control(
            'selected_icon',
            [
                'label' => esc_html__('Icon', 'BEA'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'label_block' => true,
                'default' => [
                    'value' => 'fas fa-medal',
                    'library' => 'fa-solid',
                ],
            ]
        );
        $this->add_control(
            'heading',
            [
                'label' => esc_html__( 'Heading', 'BEA' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( ' Top Rising Medal ', 'BEA' ),
                'placeholder' => esc_html__( 'Type your title here', 'BEA' ),
            ]
        );  
        $this->add_control(
            'description',
            [
                'label' => esc_html__( 'Description', 'BEA' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 10,
                'default' => esc_html__( ' This website to modify the aspects of their web interface, ensuring a more tailored and enjoyable session. ', 'BEA' ),
                'placeholder' => esc_html__( 'Type your description here', 'BEA' ),
            ]
        );

        $this->add_control(
            'box_link',
            [
                'label' => esc_html__( 'Box Link', 'BEA' ),
                'type' => \Elementor\Controls_Manager::URL,
                'options' => [ 'url', 'is_external', 'nofollow' ],
                'label_block' => true,
            ]
        );

       
        $this->add_control(
            'show_additional_text',
            [
                'label' => esc_html__( 'Additional Text', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'BEA' ),
                'label_off' => esc_html__( 'Hide', 'BEA' ),
                'return_value' => 'yes',
                'default' => '',
                
            ]
        );

        
        $this->add_control(
            'additional_text',
            [
                'label' => esc_html__( 'Text', 'BEA' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( '01.', 'BEA' ),
                'placeholder' => esc_html__( 'Type your additional text here', 'BEA' ),
                'condition' => [
                    'show_additional_text' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();
      
        //----------------------------------------------- Read More contect section-----------------------------------//

        $this->start_controls_section(
            'section_Button',
            [
                'label' => __('Read More ', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,

            ]
        );
        $this->add_control(
            'show_button',
            [
                'label' => esc_html__( 'Read More', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'BEA' ),
                'label_off' => esc_html__( 'Hide', 'BEA' ),
                'return_value' => 'yes',
                'default' => '',
                
            ]
        );
        $this->add_control(
            'button_text',
            [
                'label' => esc_html__( 'Text', 'BEA' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Read More', 'BEA' ),
                'placeholder' => esc_html__( 'Type your button text', 'BEA' ),
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'animated_button',
            [
                'label' => esc_html__( 'Animated Button', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'on', 'BEA' ),
                'label_off' => esc_html__( 'off', 'BEA' ),
                'return_value' => 'yes',
                'default' => ' ',
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );
    
        $this->add_control(
            'show_button_icon',
            [
                'label' => esc_html__( 'Icon', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'BEA' ),
                'label_off' => esc_html__( 'Hide', 'BEA' ),
                'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'button_icon',
            [
                'label' => esc_html__('Icon', 'BEA'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'label_block' => false,
                'skin' => 'inline',
                'condition' => [
                    'show_button' => 'yes',
                    'show_button_icon' => 'yes',
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
						'min' => 0,
					],
				],
                'default' => [
						'size' => 8,
						'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bea-icon-box .bea-btn ' => 'gap: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'show_button' => 'yes',
                    'show_button_icon' => 'yes',
                ],
			]
		);
        $this->end_controls_section();
        //----------------------------------------------- Floated icon Section ------------------------------------------//
        $this->start_controls_section(
            'section_icon',
            [
                'label' => __('Floating Icon', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'show_floated_icon',
            [
                'label' => esc_html__( 'Floating Icon', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'BEA' ),
                'label_off' => esc_html__( 'Hide', 'BEA' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );
        $this->add_control(
            'floated_icon',
            [
                'label' => esc_html__('Icon', 'BEA'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'label_block' => false,
                'skin' => 'inline',
                'condition' => [
                    'show_floated_icon' => 'yes',
                ],
                
            ]
        );
        $this->add_control(
            'show_icon_corners',
            [
                'label' => esc_html__( 'Icon Corners', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'BEA' ),
                'label_off' => esc_html__( 'Hide', 'BEA' ),
                'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'show_floated_icon' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();

        //--------------------------------------------------------- Style --------------------------------------------//
        //----------------------------------------------- box style section------------------------------------------//

        $this->start_controls_section(
            'section_box_style',
            [
                'label' => __('Box ', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,

            ]
        );
        $this->start_controls_tabs(
            'style_tabs'
        );
        
        $this->start_controls_tab(
            'style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'box-background',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-icon-box',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_box_shadow',
                'selector' => '{{WRAPPER}} .bea-icon-box',
            ]
        );
        $this->add_responsive_control(
            'box-zindex',
            [
                'label' => esc_html__( 'z-index', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box' => 'z-index: {{SIZE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'box_border',
                'selector' => '{{WRAPPER}} .bea-icon-box',
            ]
        );
        $this->add_control(
            'box-border-radius',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'box-padding',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'default' => [
					'top' => 40,
					'right' => 40,
					'bottom' => 40,
					'left' => 40,
					'unit' => 'px',
				],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();
        //------------------------ Hover -----------------------------//

        $this->start_controls_tab(
            'style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'box-background-hover',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-icon-box:hover',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_box_shadow_hover',
                'selector' => '{{WRAPPER}} .bea-icon-box:hover',
            ]
        );
        $this->add_responsive_control(
            'box-zindex-hover',
            [
                'label' => esc_html__( 'z-index', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover' => 'z-index: {{SIZE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'box_border_hover',
                'selector' => '{{WRAPPER}} .bea-icon-box:hover',
            ]
        );
        $this->add_control(
            'box-border-radius-hover',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'box-padding-hover',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        

        $this->end_controls_section();

        //----------------------------------------------------- icon style section---------------------------------------------------//
        $this->start_controls_section(
            'icon_section_style',
            [
                'label' => __('Icon ', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_Box_Icon' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon_alignment',
            [
                'label' => __('Icon Alignment', 'BEA'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'row' => [
                        'title' => __('Left', 'BEA'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'column' => [
                        'title' => __('Top', 'BEA'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'row-reverse' => [
                        'title' => __('Right', 'BEA'),
                        'icon' => 'eicon-h-align-right',
                    ],
                    'column-reverse' => [
                        'title' => __('Bottom', 'BEA'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'column',
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box' => 'flex-direction: {{VALUE}};',
                ],
                'condition' => [
                    'heading_position!' => 'above',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon_alignment_self',
            [
                'label' => __('Vertical Alignment', 'BEA'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('top', 'BEA'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __('Center', 'BEA'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => __('Bottom', 'BEA'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box ' => 'align-items: {{VALUE}};',
                ],
                'condition' => [
                    'icon_alignment' => ['row','row-reverse'],
                ],
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
					'size' => 60,
				],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .icon-wrapper i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-icon-box .icon-wrapper svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon-size-wrapper',
            [
                'label' => esc_html__( 'icon Wrapper', 'BEA' ),
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
                    '{{WRAPPER}} .bea-icon-box .icon' => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        //----------------------- icon normal style section -----------------------------//

        $this->start_controls_tabs(
            'icon_style_tabs'
        );
        
        $this->start_controls_tab(
            'icon_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'textdomain' ),
            ]
        );
        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__( 'Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => "#656565",
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .icon-wrapper i ' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-icon-box .icon-wrapper svg path ' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'icon_background',
				'types' => [ 'classic', 'gradient'],
				'exclude' => [ 'image'],
				'selector' => '{{WRAPPER}} .bea-icon-box .icon',
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .bea-icon-box .icon',
			]
		);
        $this->add_control(
            'icon-border-radius',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon-margin',
            [
                'label' => esc_html__( 'Margin', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 40,
					'left' => 0,
					'unit' => 'px',
				],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();
            //----------------------- icon Hover style section -----------------------------//

        $this->start_controls_tab(
            'icon_Hover_tab',
            [
                'label' => esc_html__( 'Hover', 'textdomain' ),
            ]
        );
        $this->add_control(
            'icon_color_hover',
            [
                'label' => esc_html__( 'Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .icon-wrapper i ' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-icon-box:hover .icon-wrapper svg path ' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'icon_background_hover',
				'types' => [ 'classic', 'gradient'],
				'exclude' => [ 'image'],
				'selector' => '{{WRAPPER}} .bea-icon-box:hover .icon',
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'icon_box_shadow_hover',
				'selector' => '{{WRAPPER}} .bea-icon-box:hover .icon',
			]
		);
        $this->add_control(
            'icon-border-radius-hover',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon-margin-hover',
            [
                'label' => esc_html__( 'Margin', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

        //----------------------------------------------- heading style section------------------------------------------//
        $this->start_controls_section(
            'heading_section_style',
            [
                'label' => __('Title ', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} .bea-icon-box .heading',
            ]
        );
        $this->add_responsive_control(
            'heading_Spacing',
            [
                'label' => esc_html__( 'Spacing', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 5,
                    ],
                    
                ],
                'default' => [
					'unit' => 'px',
					'size' => 25,
				],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .heading' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'heading_color',
            [
                'label' => esc_html__( 'Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => "#191919",

                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .heading' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'heading_color_hover',
            [
                'label' => esc_html__( 'Hover Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .heading' => 'color: {{VALUE}}',
                ],
            ]
        );
       
        $this->end_controls_section();
        //----------------------------------------------- Description style section------------------------------------------//
        $this->start_controls_section(
            'description_section_style',
            [
                'label' => __('Description ', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_description' => 'yes',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .bea-icon-box .description',
                
            ]
        );
        $this->add_responsive_control(
            'description_Spacing',
            [
                'label' => esc_html__( 'Spacing', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 5,
                    ],
                ],
                'default' => [
					'unit' => 'px',
					'size' => 40,
				],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .description' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'description_color',
            [
                'label' => esc_html__( 'Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => "#656565",
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .description' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'description_color_hover',
            [
                'label' => esc_html__( 'Hover Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .description' => 'color: {{VALUE}}',
                ],
            ]
        );
      
        $this->end_controls_section();
        //----------------------------------------------- bUTTON style section------------------------------------------//
        $this->start_controls_section(
            'button_section_style',
            [
                'label' => __('Read More', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,

            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .bea-icon-box .bea-btn ',
               
            ]
        );
        $this->add_control(
            'button-icon-size',
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
                    "size" =>14,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .bea-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-icon-box .bea-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->start_controls_tabs(
            'btn_tabs'
        );
        //------------------------ btn normal Text  style section------------------------//
        
        $this->start_controls_tab(
            'btn_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );
        $this->add_control(
            'button_color',
            [
                'label' => esc_html__( 'Text Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => "#000",
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .bea-btn span' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'button_icon_color',
            [
                'label' => esc_html__( 'icon Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => "#000",
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .bea-btn i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-icon-box .bea-btn svg path' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button-background',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-icon-box .bea-btn ',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .bea-icon-box .bea-btn  ',
            ]
        );
        $this->add_control(
            'button-border-radius',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .bea-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'button-padding',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .bea-btn ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
  
        //------------------------ btn hover Text  style section------------------------//


        $this->start_controls_tab(
            'btn_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );
        $this->add_control(
            'button_color_hover',
            [
                'label' => esc_html__( 'Text Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .bea-btn span' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'button_icon_color_hover',
            [
                'label' => esc_html__( 'icon Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .bea-btn i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-icon-box:hover .bea-btn svg path' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button-background-hover',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-icon-box:hover .bea-btn ',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border_hover',
                'selector' => '{{WRAPPER}} .bea-icon-box:hover .bea-btn  ',
            ]
        );
        $this->add_control(
            'button-border-radius-hover',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .bea-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'button-padding-hover',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .bea-btn ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();
        //----------------------------------------------- Additional Text  style section------------------------------------------//

        $this->start_controls_section(
            'additional_text_section_style',
            [
                'label' => __('Additional Text ', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_additional_text' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'additional_text_typography',
                'selector' => '{{WRAPPER}} .bea-icon-box .additional-text',
              
            ]
        );
        
        $this->add_control(
            'additional_text_color',
            [
                'label' => esc_html__( 'Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .additional-text' => 'color: {{VALUE}}',
                ],
            ]
        );
        $left = esc_html__( 'Left', 'elementor' );
        $right = esc_html__( 'Right', 'elementor' );

        $start = is_rtl() ? $right : $left;
        $end = ! is_rtl() ? $right : $left;

        $this->add_control(
            '_offset_orientation_h1',
            [
                'label' => esc_html__( 'Horizontal Orientation', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'default' => 'start',
                'options' => [
                    'start' => [
                        'title' => $start,
                        'icon' => 'eicon-h-align-left',
                    ],
                    'end' => [
                        'title' => $end,
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'render_type' => 'ui',
            ]
        );

        $this->add_responsive_control(
            '_offset_x1',
            [
                'label' => esc_html__( 'Offset', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vw' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vh' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => '40',
                ],
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}} .bea-icon-box .additional-text' => 'left: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}} .bea-icon-box .additional-text' => 'right: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    '_offset_orientation_h1!' => 'end',
                ],
            ]
        );

        $this->add_responsive_control(
            '_offset_x_end1',
            [
                'label' => esc_html__( 'Offset', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 0.1,
                    ],
                    '%' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vw' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vh' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => '0',
                ],
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}} .bea-icon-box .additional-text' => 'right: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}} .bea-icon-box .additional-text' => 'left: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    '_offset_orientation_h1' => 'end',
                ],
            ]
        );

        $this->add_control(
            '_offset_orientation_v1',
            [
                'label' => esc_html__( 'Vertical Orientation', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'default' => 'start',
                'options' => [
                    'start' => [
                        'title' => esc_html__( 'Top', 'elementor' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'end' => [
                        'title' => esc_html__( 'Bottom', 'elementor' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'render_type' => 'ui',
            ]
        );

        $this->add_responsive_control(
            '_offset_y1',
            [
                'label' => esc_html__( 'Offset T', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vh' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vw' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'vw', 'custom' ],
                'default' => [
                    'size' => '40',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .additional-text' => 'top: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    '_offset_orientation_v1!' => 'end',
                ],
            ]
        );

        $this->add_responsive_control(
            '_offset_y_end1',
            [
                'label' => esc_html__( 'Offset B', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vh' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vw' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'vw', 'custom' ],
                'default' => [
                    'size' => '0',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .additional-text' => 'bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    '_offset_orientation_v1' => 'end',
                    
                ],
            ]
        );
        $this->end_controls_section();
        //----------------------------------------------- Floating icon  style section------------------------------------------//

        $this->start_controls_section(
            'floating_icon_section_style',
            [
                'label' => __('Floating Icon', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_floated_icon' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'floating_icon-wrapper-size',
            [
                'label' => esc_html__( 'icon wrapper', 'BEA' ),
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
                    '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper .floated-icon ' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'floating_icon-size',
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
                    '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper .floated-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper .floated-icon svg ' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->start_controls_tabs(
            'floating_icon_tabs'
        );
        
        $this->start_controls_tab(
            'floating_icon_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );
        $this->add_control(
            'floating_icon_color',
            [
                'label' => esc_html__( 'Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper .floated-icon i ' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper .floated-icon svg path ' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'floating_icon_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => "#fff",
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper ' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-icon-box .shap-left-top svg path ,{{WRAPPER}} .bea-icon-box .shap-right-bottom svg path' => 'fill: {{VALUE}}',
                ],
            ]
        );
        
        
        $this->add_responsive_control(
            'floating_icon_opacity',
            [
                'label' => esc_html__( 'opacity', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper  ' => 'opacity: {{SIZE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'floating_icon_border',
                'selector' => '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper .floated-icon ',
            ]
        );
        $this->add_control(
            'floating_icon-border-radius-icon',
            [
                'label' => esc_html__( 'icon Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper .floated-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'floating_icon-border-radius',
            [
                'label' => esc_html__( 'Wrapper Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'floating_icon-padding',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box .floated-icon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );	
        $this->end_controls_tab();
        //------------------- Floating icon hover style section---------------//
        
        $this->start_controls_tab(
            'floating_icon_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );
        $this->add_control(
            'floating_icon_color_hover',
            [
                'label' => esc_html__( 'Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .floated-icon-wrapper .floated-icon i ' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-icon-box:hover .floated-icon-wrapper .floated-icon svg path ' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'floating_icon_bg_color_hover',
            [
                'label' => esc_html__( 'Background Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => "#fff",
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .floated-icon-wrapper ' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-icon-box:hover .shap-left-top svg path ,{{WRAPPER}} .bea-icon-box:hover .shap-right-bottom svg path' => 'fill: {{VALUE}}',
                ],
            ]
        );
    
        $this->add_responsive_control(
            'floating_icon_opacity_hover',
            [
                'label' => esc_html__( 'opacity', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .floated-icon-wrapper' => 'opacity: {{SIZE}};',
                ],
            ]
        );
        $this->add_control(
            'floating_icon-border-radius-icon-hover',
            [
                'label' => esc_html__( 'icon Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .floated-icon-wrapper .floated-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'floating_icon-border-radius-hover',
            [
                'label' => esc_html__( 'Wrapper Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .floated-icon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'floating_icon-padding-hover',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-icon-box:hover .floated-icon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );	
        $this->end_controls_tab();
        $this->end_controls_tabs();
    

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
      
        ?>
            
        <a href='<?php echo esc_url( $settings["box_link"]["url"] ); ?>' <?php if ( $settings['box_link']['is_external'] ) {echo'target="_blank"';} ?>>
            <div class="bea-icon-box">
                <?php if ($settings["show_additional_text"] =="yes"):?>
                    <span class="additional-text"><?php echo esc_html( $settings["additional_text"] ); ?>  </span>
                <?php endif; ?>

                <?php if ($settings["show_title"] =="yes" and $settings["heading_position"] =="above"):?>
                    <<?php Utils::print_validated_html_tag( $settings['bea_heading_tag'] ); ?> class="heading">
                            <?php echo esc_html($settings['heading']); ?>
                    </<?php Utils::print_validated_html_tag( $settings['bea_heading_tag'] ); ?>>
                <?php endif; ?>
                <?php if ($settings["show_Box_Icon"] =="yes"):?>
                    <div class="icon-wrapper">
                        <span class="icon">
                            <?php
                                Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
                            ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <div class="box-content <?php echo'animated-'.esc_attr($settings['animated_description']);?>">
                    <?php if ($settings["show_title"] =="yes" and $settings["heading_position"] =="under"):?>
                        <<?php Utils::print_validated_html_tag( $settings['bea_heading_tag'] ); ?> class="heading">
                            <?php echo esc_html($settings['heading']); ?>
                        </<?php Utils::print_validated_html_tag( $settings['bea_heading_tag'] ); ?>>
                    <?php endif; ?>
                    <?php if ($settings["show_description"] =="yes"):?>

                        <p class="description">
                            <?php echo esc_html( $settings["description"] ); ?>
                        </p>
                     <?php endif; ?>

                    <?php if ($settings["show_button"] =="yes"):?>
                        <div class="btn-wrapper">
                            <div class="bea-btn <?php if ($settings["animated_button"] =="yes"){echo "text-slide-in";}?>">
                                <span class="text"> <?php echo esc_html( $settings["button_text"] ); ?> </span>
                                <?php if ($settings["show_button_icon"] =="yes"):
                                    Icons_Manager::render_icon($settings['button_icon'], ['aria-hidden' => 'true']);
                                endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($settings["show_floated_icon"] =="yes"):?>
                        <div class="floated-icon-wrapper">
                            <div class="floated-icon">
                                <?php Icons_Manager::render_icon($settings['floated_icon'], ['aria-hidden' => 'true']);?>
                            </div>
                            <?php if ($settings["show_icon_corners"] =="yes"):?>
                                <div class="shap-left-top">
                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-11 h-11">
                                        <path d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z" fill="#fff"></path>
                                    </svg>
                                </div>
                                <div class="shap-right-bottom">
                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-11 h-11">
                                        <path d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z" fill="#fff"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </a>

        <?php
    }

}

