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
class Better_Heading_adv extends Widget_Base
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
        return 'better-heading-adv';
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
        return __('Heading Advanced', 'BEA');
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
        return 'eicon-t-letter bea-widget-badge';
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

          //----------------------------------------------- heading Box content section-----------------------------------//


        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Title', 'BEA'),
            ]
        );

        $this->add_control(
			'heading_text',
			[
				'label' => esc_html__( 'Title', 'BEA' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'BEA Advanced {{Heading}}', 'BEA' ),
				'placeholder' => esc_html__( 'Type your Text here', 'BEA' ),
				'label_block' => true,
				'description' => esc_html__( '"Focused Title" Settings will be worked, If you use this {{something}} format', 'BEA' ),
			]
		);

        $this->add_control(
			'sub_title',
			[
				'label' => esc_html__( 'Sub Title', 'BEA' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'BEA-Lip' ),
				
			]
		);
        $this->add_responsive_control(
			'heading_align',
			[
				'label' => esc_html__( 'Alignment', 'BEA' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'BEA' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'BEA' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'BEA' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
                'toggle' => true,

				
			]
		);
        $this->add_control(
            'sub_title_position',
            [
                'label' => esc_html__( 'Sub Title Position', 'BEA' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__( 'Before Title', 'BEA' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__( 'After Title', 'BEA' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'bottom',
                'toggle' => true,
            ]
        );
        $this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SELECT,
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
				'default' => 'h2',
			]
		);
        $this->end_controls_section();
		//----------------------------------------------- Shadow title section-----------------------------------//

        $this->start_controls_section(
            'shadow_text_section',
            [
			    'label' => esc_html__( 'Shadow Text', 'BEA' )
		    ]
        );

		$this->add_control(
            'show_shadow_text', 
            [
                'label' => esc_html__( 'Show Shadow Text', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]   
        );

		$this->add_control(
            'shadow_title_content',
            [
                'label'			 => esc_html__( 'Text', 'BEA' ),
                'label_block'	 => true,
                'type'			 => \Elementor\Controls_Manager::TEXT,
                'default'		 => esc_html__( 'Advanced', 'BEA' ),
                'condition' => [
                    'show_shadow_text' => 'yes'
                ],

            ]
        );
		$this->end_controls_section();
        
        $this->start_controls_section(
            'heading_section_seperator',
            [
                'label' => __('Separator', 'BEA'),
            ]
        );

		$this->add_control(
			'heading_show_seperator', [
				'label'			 =>esc_html__( 'Show Separator', 'BEA' ),
				'type'			 => \Elementor\Controls_Manager::SWITCHER,
				'label_on' =>esc_html__( 'Yes', 'BEA' ),
				'label_off' =>esc_html__( 'No', 'BEA' ),
			]
		);
		$this->add_control(
			'heading_seperator_style',
			[
				'label' => esc_html__( 'Separator Style', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'border-divider' => esc_html__( 'Dotted', 'BEA' ),
					'separator-border' => esc_html__( 'Solid', 'BEA' ),
					'border_custom' => esc_html__( 'Custom', 'BEA' ),
				],
				'default' => 'border-divider',
				'condition' => [
					'heading_show_seperator' => 'yes',
				],
			]
		);

		$this->add_control(
			'heading_seperator_position',
			[
				'label' => esc_html__( 'Separator Position', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'top' => esc_html__( 'Top', 'BEA' ),
					'before' => esc_html__( 'Before Title', 'BEA' ),
					'after' => esc_html__( 'After Title', 'BEA' ),
					'bottom' => esc_html__( 'Bottom', 'BEA' ),
				],
				'default' => 'after',
				'condition' => [
					'heading_show_seperator' => 'yes',
				],
			]
		);

		$this->add_control(
			'heading_seperator_image',
			[
				'label' => esc_html__( 'Choose Image', 'BEA' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
					'id'    => -1
				],
				'condition' => [
					'heading_show_seperator' => 'yes',
					'heading_seperator_style' => 'border_custom',
				],

			]
		);

		$this->add_group_control(
            \Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'heading_seperator_image_size',
				'default' => 'large',
				'condition' => [
					'heading_show_seperator' => 'yes',
					'heading_seperator_style' => 'border_custom',
				],
            ]
        );

		$this->end_controls_section();
		
		//----------------------------------------------- heading title section-----------------------------------//
        $this->start_controls_section(
            'heading_title_style',
            [
                'label' => esc_html__( 'Title', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'		 => 'heading_title_typography',
                'selector'	 => '{{WRAPPER}} .bea-heading-adv .title',
            ]
        );

        $this->add_control(
            'heading_title_color', 
            [
                'label' => esc_html__('Title Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-heading-adv .title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'heading_title_color_hover', 
            [
                'label' => esc_html__('Hover Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-heading-adv .title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'title-background',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bea-heading-adv ',
			]
		);        
        $this->add_group_control(
			\Elementor\Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_stroke',
				'selector' => '{{WRAPPER}} .bea-heading-adv .title',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .bea-heading-adv .title',
			]
		);
        $this->add_control(
            'title-fill-toggle',
            [
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label' => esc_html__( 'Image & Gradient Mask', 'BEA' ),
                'label_off' => esc_html__( 'Default', 'BEA' ),
                'label_on' => esc_html__( 'Custom', 'BEA' ),
                'return_value' => 'yes',
            ]
        );
       
        $this->start_popover();
        
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'title-mask-background',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bea-heading-adv .title-mask',
			]
		);
        $this->end_popover();
        
      
        $this->add_responsive_control(
            'heading_title_padding',
            [
                'label'      => esc_html__('Padding', 'BEA'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-heading-adv .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'heading_title_margin',
            [
                'label'      => esc_html__('Margin', 'BEA'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'default'    => [
                    'unit'     => 'px',
                    'top'      => 0,
                    'right'    => 0,
                    'bottom'   => 10,
                    'left'     => 0,
                    'isLinked' => true,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-heading-adv .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
        //----------------------------------------------- heading Shadow title section-----------------------------------//
        $this->start_controls_section(
            'heading_focused_title_style',
            [
                'label' => esc_html__( 'Focused Title', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'		 => 'heading_focused_title_typography',
                'selector'	 => '{{WRAPPER}} .bea-heading-adv .title span',
            ]
        );
        $this->add_control(
            'heading_focused_title_color', 
            [
                'label' => esc_html__('Title Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-heading-adv .title span' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'focused_title_stroke',
				'selector' => '{{WRAPPER}} .bea-heading-adv .title span',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'focused_title_shadow',
				'selector' => '{{WRAPPER}} .bea-heading-adv .title span',
			]
		);
        $this->end_controls_section();
        //----------------------------------------------- heading sub-title section-----------------------------------//
        $this->start_controls_section(
            'heading_sub_title_style',
            [
                'label' => esc_html__( 'Sub Title', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'		 => 'heading_sub_title_typography',
                'selector'	 => '{{WRAPPER}} .bea-heading-adv .sub-title',
            ]
        );

        $this->add_control(
            'heading_sub_title_color', 
            [
                'label' => esc_html__('Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-heading-adv .sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'heading_sub_title_color_hover', 
            [
                'label' => esc_html__('Hover Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-heading-adv .sub-title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'sub-title-background',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bea-heading-adv .sub-title',
			]
		);
        $this->add_control(
			'sub-title-rotate',
			[
				'label' => esc_html__( 'Rotate', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'deg' => [
						'min' => -360,
						'max' => 360,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bea-heading-adv .sub-title' => 'transform: rotate({{SIZE}}deg);',
				],
			]
		);
        $this->add_control(
			'sub-title-z-index',
			[
				'label' => esc_html__( 'Z-index', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'deg' => [
						'min' => 0,
						'max' => 10,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bea-heading-adv .sub-title' => 'z-index: {{SIZE}};',
				],
			]
		);
        $this->add_responsive_control(
            'heading_sub_title_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'BEA'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-heading-adv .sub-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'heading_sub_title_padding',
            [
                'label'      => esc_html__('Padding', 'BEA'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-heading-adv .sub-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'heading_sub_title_margin',
            [
                'label'      => esc_html__('Margin', 'BEA'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-heading-adv .sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        //----------------------------------------------- heading Shadow title section-----------------------------------//
        $this->start_controls_section(
            'heading_shadow_title_style',
            [
                'label' => esc_html__( 'Shadow Title', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
					'show_shadow_text' => 'yes'
				]
            ]
        );
        $this->add_responsive_control( 
            'shadow_text_position',
            [
			'label' => esc_html__( 'Position', 'BEA' ),
			'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%','rem', 'custom' ],
			'allowed_dimensions' => [ 'bottom', 'left' ],
			'default' => [
				'bottom' => '2',
				'left' => '50',
				'unit' => 'px',
				'isLinked' => false
			],
			'selectors' => [
				'{{WRAPPER}} .bea-heading-adv .shadow-title' => 'bottom:{{BOTTOM}}{{UNIT}};left:{{LEFT}}{{UNIT}};',
			],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'		 => 'heading_shadow_title_typography',
                'selector'	 => '{{WRAPPER}} .bea-heading-adv .shadow-title',
            ]
        );
        $this->add_control(
            'heading_shadow_title_color', 
            [
                'label' => esc_html__('Title Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-heading-adv .shadow-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'popover-Text-stroke',
            [
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label' => esc_html__( 'Text Stroke', 'BEA' ),
                'label_off' => esc_html__( 'Default', 'BEA' ),
                'label_on' => esc_html__( 'Custom', 'BEA' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->start_popover();
        
       
        $this->add_control( 
            'shadow_text_border_width',
            [
			'label' => __( 'Stroke Width', 'BEA' ),
			'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%','rem', 'custom' ],

			'range' => [
				'px' => [
					'min' => 0,
					'max' => 64,
					'step' => 1,
				]
			],
			'default' => [ 'unit' => 'px', 'size' => 1 ],
			'selectors' => [
				'{{WRAPPER}} .bea-heading-adv .shadow-title' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};'
			],
            ]
        );

		$this->add_responsive_control( 
            'shadow_text_border_color',
            [
			'label'		 =>esc_html__( 'Stroke Color', 'BEA' ),
			'type'		 => Controls_Manager::COLOR,
			'default' => "#00000042",
			'selectors'	 => [
				'{{WRAPPER}} .bea-heading-adv .shadow-title' => '-webkit-text-stroke-color: {{VALUE}};',
			],
            ]
        );
        $this->end_popover();
        
        $this->end_controls_section();
        $this->start_controls_section(
			'heading_section_seperator_style', [
				'label'	 => esc_html__( 'Separator', 'BEA' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
				'condition' => [
					'heading_show_seperator' => 'yes'
				]
			]
		);
		$this->add_responsive_control(
			'heading_seperator_width',
			[
				'label' => esc_html__( 'Width', 'BEA' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .bea-heading-adv .heading-border-divider' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bea-heading-adv .heading-separator-border' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'		=> [
					'heading_seperator_style!' => 'border_custom'
				]
			]
		);

		$this->add_responsive_control(
			'heading_seperator_height',
			[
				'label' => esc_html__( 'Height', 'BEA' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .bea-heading-adv .heading-border-divider, {{WRAPPER}}  .bea-heading-adv .heading-border-divider::before' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bea-heading-adv .heading-separator-border' => 'height: {{SIZE}}{{UNIT}};',
					
				],
				'condition'		=> [
					'heading_seperator_style!' => 'border_custom'
				]
			]
		);
        $this->add_responsive_control(
			'heading_seperator_color', [
				'label'		 =>esc_html__( 'Separator color', 'BEA' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .bea-heading-adv .heading-border-divider' => 'background: {{VALUE}};',
					'{{WRAPPER}} .bea-heading-adv .heading-border-divider::before' => 'background: {{VALUE}};color: {{VALUE}};',
					'{{WRAPPER}} .bea-heading-adv .heading-separator-border' => 'background: {{VALUE}};',
				],
				'condition'		=> [
					'heading_seperator_style!' => 'border_custom'
				]
			]
		);

		$this->add_responsive_control(
			'heading_seperator_margin',
			[
				'label' => esc_html__( 'Margin', 'BEA' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],

				'selectors' => [
					'{{WRAPPER}} .bea-heading-adv .separetor-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        $title_text = str_replace( array( '{{', '}}' ), array( '<span>', '</span>' ), $settings["heading_text"] );
        ?>
        <div class="bea-heading-adv <?php echo esc_attr(' bea-text-' . $settings["heading_align"]); ?>">
            <?php if($settings["heading_seperator_position"]=="top"):?>
                <div class="separetor-wrapper">

                    <?php if( $settings["heading_seperator_style"]=="border_custom"){
                        echo  \Elementor\Group_Control_Image_Size::get_attachment_image_html($settings, 'heading_seperator_image_size', 'heading_seperator_image');
                    }else{?>
                    <div class="heading-<?php echo esc_attr( $settings["heading_seperator_style"] );?><?php echo esc_attr(' bea-postion-' . $settings["heading_align"]); ?>"></div>
                   <?php }?>
                </div>
            <?php endif; ?>
        
            <span class="shadow-title">
                <?php echo esc_html( $settings["shadow_title_content"] ); ?>
            </span>

            <?php if($settings["sub_title_position"]=="top" and !empty( $settings["sub_title"] ) ): ?>
                <div class="sub-title"><?php echo esc_html( $settings["sub_title"] ); ?></div>
            <?php endif; ?>

            <?php if($settings["heading_seperator_position"]=="before"):?>
                <div class="separetor-wrapper">
                    <?php if( $settings["heading_seperator_style"]=="border_custom"){
                        echo  \Elementor\Group_Control_Image_Size::get_attachment_image_html($settings, 'heading_seperator_image_size', 'heading_seperator_image');
                    }else{?>
                    <div class="heading-<?php echo esc_attr( $settings["heading_seperator_style"] );?><?php echo esc_attr(' bea-postion-' . $settings["heading_align"]); ?>"></div>
                    <?php }?>
                </div>
            <?php endif; ?>

            <<?php Utils::print_validated_html_tag( $settings['title_tag'] ); ?> class="title <?php if($settings["title-fill-toggle"]=="yes"){echo 'title-mask';} ?> " >
                <?php echo wp_kses( $title_text, ['span'=> array()] ); ?>
            </<?php Utils::print_validated_html_tag( $settings['title_tag'] ); ?>>

            <?php if($settings["heading_seperator_position"]=="after"):?>
                <div class="separetor-wrapper">
                    <?php if( $settings["heading_seperator_style"]=="border_custom"){
                        echo  \Elementor\Group_Control_Image_Size::get_attachment_image_html($settings, 'heading_seperator_image_size', 'heading_seperator_image');
                    }else{?>
                    <div class="heading-<?php echo esc_attr( $settings["heading_seperator_style"] );?><?php echo esc_attr(' bea-postion-' . $settings["heading_align"]); ?>"></div>
                    <?php }?>
                </div>
            <?php endif; ?>
            <?php if($settings["sub_title_position"]=="bottom" and !empty( $settings["sub_title"] ) ): ?>
                <div class="sub-title"><?php echo esc_html( $settings["sub_title"] ); ?></div>
            <?php endif; ?>
            <?php if($settings["heading_seperator_position"]=="botton"):?>
                <div class="separetor-wrapper">
                    <?php if( $settings["heading_seperator_style"]=="border_custom"){
                        echo  \Elementor\Group_Control_Image_Size::get_attachment_image_html($settings, 'heading_seperator_image_size', 'heading_seperator_image');
                    }else{?>
                    <div class="heading-<?php echo esc_attr( $settings["heading_seperator_style"] );?><?php echo esc_attr(' bea-postion-' . $settings["heading_align"]); ?>"></div>
                    <?php }?>
                </div>
            <?php endif; ?>
        </div>
       
        <?php
    }

}

