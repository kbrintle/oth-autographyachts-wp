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
class Better_Flip_Box extends Widget_Base
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
        return 'better-flip-box';
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
        return __('Flip Box', 'BEA');
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
        return 'eicon-flip-box bea-widget-badge';
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

          //----------------------------------------------- flip Box content section-----------------------------------//


        $this->start_controls_section(
            'section_settings',
            [
                'label' => __('Flip Box', 'BEA'),
            ]
        );
        $this->add_control(
			'flip_box_style',
			[
				'label'=> esc_html__('Flip Style', 'BEA'),
				'type'=> \Elementor\Controls_Manager::SELECT,
				'default' => 'flip-animate',
				'options'=> [
					'flip-animate' => esc_html__('Flip', 'BEA'),
					'slide-animate' => esc_html__('Slide', 'BEA'),
                    'zoom-animate' => esc_html__('Zoom', 'BEA'),
                    'flip-3d-animate' => esc_html__('3D', 'BEA'),
                    'push-animate' => esc_html__('Push', 'BEA'),
                    'transform-flip-animate' => esc_html__('Transform', 'BEA'),
                    'fade-animate' => esc_html__('Fade', 'BEA'),
                    'angle-flip' => esc_html__('Angle ', 'BEA'),
				],
			]
		);

        $this->add_control(
			'flip_box_direction',
			[
				'label' => esc_html__( 'Direction', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'left-to-right',
				'options' => [
					'left-to-right'  => esc_html__( 'Left To Right', 'BEA' ),
					'right-to-left' => esc_html__( 'Right To Left', 'BEA' ),
					'top-to-bottom' => esc_html__( 'Top To Bottom', 'BEA' ),
					'bottom-to-top' => esc_html__( 'Bottom To Top', 'BEA' ),
				],
                'condition' => [
                    'flip_box_style!' => [ 'zoom-animate', 'fade-animate','angle-flip'],
                ],
			]
		);

        $this->add_control(
			'flip_box_zoom_direction',
			[
				'label' => esc_html__( 'Direction', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'zoom-up',
				'options' => [
                    'zoom-up' => esc_html__( 'Zoom In', 'BEA' ),
                    'zoom-out' => esc_html__( 'Zoom Out', 'BEA' ),
				],
                'condition' => [
                    'flip_box_style' => 'zoom-animate',
                ],
			]
		);
        $this->add_control(
			'flip_box_angle_direction',
			[
				'label' => esc_html__( 'Direction', 'BEA' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'normal',
				'options' => [
                    'normal' => esc_html__( 'Normal', 'BEA' ),
                    'reverse' => esc_html__( 'Reverse', 'BEA' ),
                    'rotate' => esc_html__( 'Rotate', 'BEA' ),
                    'rotate-reverse' => esc_html__( 'Reverse Rotate', 'BEA' ),
				],
                'condition' => [
                    'flip_box_style' => 'angle-flip',
                ],
			]
		);

        $this->start_controls_tabs(
            'content-tabs'
        );
        
        $this->start_controls_tab(
            'front_tab',
            [
                'label' => esc_html__( 'Front', 'BEA' ),
            ]
        );
        
        $this->add_control(
            'front_media_position',
            [
                'label' => esc_html__( 'Media Position', 'BEA' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__( 'Top', 'BEA' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__( 'Bottom', 'BEA' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'top',
                'toggle' => true,
            ]
        );
        $this->add_control(
            'flip_front_media_type', 
            [
                'label'       => esc_html__( 'Media Type', 'BEA' ),
                'type'        => \Elementor\Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'none' => [
                        'title' => esc_html__( 'None', 'BEA' ),
                        'icon'  => 'fa fa-ban',
                    ],
                    'icon' => [
                        'title' => esc_html__( 'Icon', 'BEA' ),
                        'icon'  => 'fa fa-paint-brush',
                    ],
                    'image' => [
                        'title' => esc_html__( 'Image', 'BEA' ),
                        'icon'  => 'fa fa-image',
                    ],
                ],
                'toggle' => false,
                
                'default'=> 'icon',
            ]
        );

        $this->add_control(
            'flip_front_icon',
            [
                'label' => esc_html__( 'Icon', 'BEA' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-medal',
                    'library' => 'fa-solid',
                ],
                'label_block' => true,
                'condition' => [
                    'flip_front_media_type' => 'icon',
				]
            ]
        );

        $this->add_control(
			'flip_front_image',
			[
				'label' => esc_html__( 'Choose Image', 'BEA' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
                'condition' => [
                    'flip_front_media_type' => 'image',
				]
			]
		);

        $this->add_group_control(
            \Elementor\Group_Control_Image_Size::get_type(),
			[
				'name' => 'flip_front_thumbnail',
				'default' => 'Large',
                'condition' => [
                    'flip_front_media_type' => 'image',
				]
			]
		);

        $this->add_control(
            'flip_front_title',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label' => esc_html__( 'Title', 'BEA' ),
                'default' => esc_html__('BEA Flip Box', 'BEA'),
            ]
        );

        $this->add_control(
			'flip_front_title_tag',
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
				'default' => 'h4',
			]
		);

        $this->add_control(
            'flip_front_sub_title',
            [
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'label' => esc_html__( 'Sub Title', 'BEA' ),
                'default' => esc_html__('Amazingly on mouse hover', 'BEA'),
                'rows' => 2,
                'label_block' => true,
            ]
        );

        $this->add_control(
			'flip_front_description',
			[
				'label' => esc_html__( 'Flip Description', 'BEA' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 3,
				'label_block'	 => true,
				'default'	 => esc_html__( 'A flip box is a box that flips over when you hover over it.', 'BEA' ),
				'placeholder' => esc_html__( 'Box Description', 'BEA' ),
			]
		);

        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'back_tab',
            [
                'label' => esc_html__( 'Back', 'BEA' ),
            ]
        );
        $this->add_control(
            'back_media_position',
            [
                'label' => esc_html__( 'Media Position', 'BEA' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__( 'Top', 'BEA' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__( 'Bottom', 'BEA' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'top',
                'toggle' => true,
            ]
        );
        
        $this->add_control(
            'flip_back_media_type', 
            [
                'label'=> esc_html__( 'Media Type', 'BEA' ),
                'type'=> Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'=> [
                    'none' => [
                        'title' => esc_html__( 'None', 'BEA' ),
                        'icon'  => 'fa fa-ban',
                    ],
                    'icon' => [
                        'title' => esc_html__( 'Icon', 'BEA' ),
                        'icon'  => 'fa fa-paint-brush',
                    ],
                    'image' => [
                        'title' => esc_html__( 'Image', 'BEA' ),
                        'icon'  => 'fa fa-image',
                    ],
                ],
                'toggle' => false,
                'default'=> 'icon',
            ]
        );

        $this->add_control(
            'flip_back_icon',
            [
                'label' => esc_html__( 'Icon', 'BEA' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'label_block' => true,
                'condition' => [
                    'flip_back_media_type' => 'icon',
				]
            ]
        );

        $this->add_control(
			'flip_back_image',
			[
				'label' => esc_html__( 'Choose Image', 'BEA' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
                'condition' => [
                    'flip_back_media_type' => 'image',
				]
			]
		);

        $this->add_group_control(
            \Elementor\Group_Control_Image_Size::get_type(),
			[
				'name' => 'flip_back_thumbnail',
				'default' => '',
                'condition' => [
                    'flip_back_media_type' => 'image',
				]
			]
		);

        $this->add_control(
            'flip_back_title',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__( 'Title', 'BEA' ),
                'default' => esc_html__('BEA-LIP', 'BEA'),
            ]
        );

        $this->add_control(
			'flip_back_title_tag',
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
				'default' => 'h4',
			]
		);

        $this->add_control(
            'flip_back_sub_title',
            [
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'label' => esc_html__( 'Sub Title', 'BEA' ),
                'default' => esc_html__('Create Your Website', 'BEA'),
                'rows' => 2,
                'label_block' => true,
            ]
        );

        $this->add_control(
			'flip_back_description',
			[
				'label' => esc_html__( 'Flip Description', 'BEA' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 3,
				'label_block'=> true,
				'placeholder' => esc_html__( 'Title Description', 'BEA' ),
			]
		);



        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();




        //----------------------------------------------- flip box back  button section-----------------------------------//

        $this->start_controls_section(
            'button_section',
            [
                'label' => __('Button', 'BEA'),
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
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'button_text',
            [
                'label' => esc_html__( 'Text', 'BEA' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Show More', 'BEA' ),
                'placeholder' => esc_html__( 'Type your button text', 'BEA' ),
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'box_link',
            [
                'label' => esc_html__( 'Box Link', 'BEA' ),
                'type' => \Elementor\Controls_Manager::URL,
                'options' => [ 'url', 'is_external', 'nofollow' ],
                'label_block' => true,
                'default' => [
                    'url' => '#0',
				],
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
					'{{WRAPPER}} .bea-flip-box .bea-btn ' => 'gap: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'show_button' => 'yes',
                    'show_button_icon' => 'yes',
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
                'default' => 'yes',
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();




        //----------------------------------------------- flip box back Social links section-----------------------------------//

        $this->start_controls_section(
            'social_links_section',
            [
                'label' => __('Social links', 'BEA'),
            ]
        );
        $this->add_control(
            'show_social_links',
            [
                'label' => esc_html__( 'Social Links', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'BEA' ),
                'label_off' => esc_html__( 'Hide', 'BEA' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
			'social_links_icon',
			[
				'label' => esc_html__( 'Icon', 'BEA' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'fa4compatibility' => 'social',
                'recommended' => [
					'fa-brands' => [
						'android',
						'apple',
						'behance',
						'bitbucket',
						'codepen',
						'delicious',
						'deviantart',
						'digg',
						'dribbble',
						'elementor',
						'facebook',
						'facebook-f',
						'flickr',
						'foursquare',
						'free-code-camp',
						'github',
						'gitlab',
						'globe',
						'houzz',
						'instagram',
						'jsfiddle',
						'linkedin',
						'medium',
						'meetup',
						'mix',
						'mixcloud',
						'odnoklassniki',
						'pinterest',
						'product-hunt',
						'reddit',
						'shopping-cart',
						'skype',
						'slideshare',
						'snapchat',
						'soundcloud',
						'spotify',
						'stack-overflow',
						'steam',
						'telegram',
						'thumb-tack',
						'tripadvisor',
						'tumblr',
						'twitch',
						'twitter',
						'viber',
						'vimeo',
						'vk',
						'weibo',
						'weixin',
						'whatsapp',
						'wordpress',
						'xing',
						'yelp',
						'youtube',
						'500px',
					],
					'fa-solid' => [
						'envelope',
						'link',
						'rss',
					],
				],
                'default' => [
                    'value' => 'fab fa-facebook',
                    'library' => 'fa-brands',
                ],
			]
		);
        $repeater->add_control(
			'social_links_icon_link',
			[
				'label' => esc_html__( 'Link', 'BEA' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'is_external' => 'true',
                    'url' => '#0',
				],
			]
		);
        $this->add_control(
			'social_links_list',
			[
				'label' => esc_html__( 'Items', 'BEA' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '<# var migrated = "undefined" !== typeof __fa4_migrated, social = ( "undefined" === typeof social ) ? false : social; #>{{{ elementor.helpers.getSocialNetworkNameFromIcon( social_links_icon, social, true, migrated, true ) }}}',
                'default' => [
					[
						'social_links_icon' => [
							'value' => 'fab fa-facebook',
							'library' => 'fa-brands',
						],
                        'social_links_icon_link' =>'#0',
					],
					[
						'social_links_icon' => [
							'value' => 'fab fa-twitter',
							'library' => 'fa-brands',
						],
                        'social_links_icon_link' =>'#0',
					],
					[
						'social_links_icon' => [
							'value' => 'fab fa-youtube',
							'library' => 'fa-brands',
						],
                        'social_links_icon_link' =>'#0',
					],
				],
                'condition' => [
                    'show_social_links' => 'yes',
                ],
			]
		);
        $this->end_controls_section();

		//----------------------------------------------- flip box Wrapper section-----------------------------------//
        $this->start_controls_section(
            'flip_box_layout_style',
            [
                'label' => esc_html__( 'Wrapper', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'flip_box_height',
            [
                'label' => esc_html__( 'Flip Box Height', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 800,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 380,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-wid-con .bea-flip-box'    => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'bea_flip_box_bg_tabs'
        );
        //----------------------------------------------- flip box front Wrapper tab-----------------------------------//

        $this->start_controls_tab(
            'flip_box_front_bg_tab',
            [
                'label' => esc_html__( 'Front', 'BEA' ),
            ]
        );

        $this->add_control(
			'flip_box_front_align',
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
				'selectors' => [
					'{{WRAPPER}} .bea-wid-con .bea-flip-box-inner .bea-flip-box-front' => 'text-align: {{VALUE}};',
				],
			]
		);
        $this->add_responsive_control(
            'flip_box_front_justify',
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
                    '{{WRAPPER}} .bea-flip-box .bea-flip-box-front' => 'align-items: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'flip_box_front_bg',
                'label' => esc_html__( 'Background', 'BEA' ),
                'types' => [ 'classic', 'gradient'],
                'exclude' => ['video'],
                'selector' => '{{WRAPPER}} .bea-flip-box-front',
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'flip_box_front_shadow',
                'selector' => '{{WRAPPER}} .bea-wid-con .bea-flip-box-inner .bea-flip-box-front',
               
			]
		);

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'flip_box_front_border_group',
                'label' => esc_html__( 'Border', 'BEA' ),
                'selector' => '{{WRAPPER}} .bea-wid-con .bea-flip-box-inner .bea-flip-box-front',
            ]
        );

        $this->add_responsive_control(
            'flip_box_front_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ,'custom' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-wid-con .bea-flip-box-inner .bea-flip-box-front' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'flip_box_front_padding',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'default' =>     [
                    'top' => '30',
                    'right' => '30',
                    'bottom' => '30',
                    'left' => '30',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-wid-con .bea-flip-box-inner .bea-flip-box-front' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();
        //----------------------------------------------- flip box back Wrapper tab-----------------------------------//

        $this->start_controls_tab(
            'flip_box_back_bg_tab',
            [
                'label' => esc_html__( 'Back', 'BEA' ),
            ]
        );

        $this->add_control(
			'flip_box_back_align',
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
				'selectors' => [
					'{{WRAPPER}} .bea-wid-con .bea-flip-box-inner .bea-flip-box-back' => 'text-align: {{VALUE}};',
				],
			]
		);
        $this->add_responsive_control(
            'flip_box_back_justify',
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
				'default' => 'center',

                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .bea-flip-box-back' => 'align-items: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'flip_box_back_bg',
                'label' => esc_html__( 'Background', 'BEA' ),
                'types' => [ 'classic', 'gradient'],
                'exclude' => ['video'],
                'selector' => '{{WRAPPER}} .bea-flip-box-back',
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'flip_box_back_shadow',
                'selector' => '{{WRAPPER}} .bea-wid-con .bea-flip-box-inner .bea-flip-box-back',
			]
		);

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'flip_box_back_border_group',
                'label' => esc_html__( 'Border', 'BEA' ),
                'selector' => '{{WRAPPER}} .bea-wid-con .bea-flip-box-inner .bea-flip-box-back',
            ]
        );

        $this->add_responsive_control(
            'flip_box_back_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ,'custom' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-wid-con .bea-flip-box-inner .bea-flip-box-back' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'flip_box_back_padding',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'default' =>[
                    'top' => '30',
                    'right' => '30',
                    'bottom' => '30',
                    'left' => '30',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-wid-con .bea-flip-box-inner .bea-flip-box-back' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

        //----------------------------------------------- flip box icon section-----------------------------------//
        $this->start_controls_section(
            'flip_icon_style',
            [
                'label' => esc_html__( 'Icon', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'flip_front_media_type',
                            'operator' => '===',
                            'value' => 'icon',
                        ],
                        [
                            'name' => 'flip_back_media_type',
                            'operator' => '===',
                            'value' => 'icon',
                        ],
                    ],
                ],
            ]
        );
        $this->start_controls_tabs(
            'flip_icon_tabs'
        );
        //----------------------------------------------- flip box front icon-----------------------------------//

        $this->start_controls_tab(
            'flip_icon_front_tab',
            [
                'label' => esc_html__( 'Front', 'BEA' ),
            ]
        );

        $this->add_control(
            'flip_front_icon_size',
            [
                'label'=> esc_html__('Icon Font Size', 'BEA'),
                'type'=> \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 150,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 60,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-flip-box .front-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-flip-box .front-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'flip_front_icon_color', 
            [
                'label' => esc_html__('Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .front-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bea-flip-box .front-icon svg path ' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'flip_front_icon_style_bg_group',
                'label' => esc_html__( 'Background', 'BEA' ),
                'types' => [ 'classic', 'gradient'],
                'exclude' => [ 'image'],
                'selector' => '{{WRAPPER}} .bea-flip-box .front-icon',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'flip_front_icon_border',
                'label' => esc_html__( 'Border', 'BEA' ),
                'selector' => '{{WRAPPER}} .bea-flip-box .front-icon',
            ]
        );
        $this->add_responsive_control(
            'flip_front_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' =>[ 'px', '%','rem', 'custom' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                    'unit'     => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .front-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'flip_front_icon_padding',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'default' =>     [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .front-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'flip_front_icon_margin',
            [
                'label'      => esc_html__('Margin', 'BEA'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default'    => [
                    'unit'     => 'px',
                    'top'      => 0,
                    'right'    => 0,
                    'bottom'   => 10,
                    'left'     => 0,
                    'isLinked' => true,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-flip-box .front-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );


        $this->end_controls_tab();

        //----------------------------------------------- flip box back icon-----------------------------------//

        $this->start_controls_tab(
            'flip_icon_back_tab',
            [
                'label' => esc_html__( 'Back', 'BEA' ),
            ]
        );

        $this->add_control(
            'flip_back_icon_size',
            [
                'label'=> esc_html__('Icon Font Size', 'BEA'),
                'type'=> \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 150,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 60,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-flip-box .back-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-flip-box .back-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'flip_back_icon_color', 
            [
                'label' => esc_html__('Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .back-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bea-flip-box .back-icon svg path ' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'flip_back_icon_style_bg_group',
                'label' => esc_html__( 'Background', 'BEA' ),
                'types' => [ 'classic', 'gradient'],
                'exclude' => [ 'image'],
                'selector' => '{{WRAPPER}} .bea-flip-box .back-icon',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'flip_back_icon_border',
                'label' => esc_html__( 'Border', 'BEA' ),
                'selector' => '{{WRAPPER}} .bea-flip-box .back-icon',
            ]
        );
        $this->add_responsive_control(
            'flip_back_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '5',
                    'bottom'   => '5',
                    'left'     => '5',
                    'unit'     => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .back-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'flip_back_icon_padding',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'default' =>     [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .back-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'flip_back_icon_margin',
            [
                'label'      => esc_html__('Margin', 'BEA'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default'    => [
                    'unit'     => 'px',
                    'top'      => 0,
                    'right'    => 0,
                    'bottom'   => 10,
                    'left'     => 0,
                    'isLinked' => true,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-flip-box .back-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );


        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        //----------------------------------------------- flip box image section-----------------------------------//
        $this->start_controls_section(
            'flip_image_style',
            [
                'label' => esc_html__( 'Image', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'flip_image_tabs'
        );
        //----------------------------------------------- flip box front image tab-----------------------------------//

        $this->start_controls_tab(
            'flip_image_front_tab',
            [
                'label' => esc_html__( 'Front', 'BEA' ),
            ]
        );

        $this->add_control(
            'flip_front_image_width',
            [
                'label'=> esc_html__('Width', 'BEA'),
                'type'=> \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 700,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => 60,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-flip-box .front-image img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'flip_front_image_height',
            [
                'label'=> esc_html__('Height', 'BEA'),
                'type'=> \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 700,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-flip-box .front-image img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'flip_front_image_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .front-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'flip_front_image_padding',
            [
                'label'=> esc_html__('Padding', 'BEA'),
                'type'=> \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-flip-box .front-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();

        //----------------------------------------------- flip box back image tab-----------------------------------//

        $this->start_controls_tab(
            'flip_image_back_tab',
            [
                'label' => esc_html__( 'Back', 'BEA' ),
            ]
        );

        $this->add_control(
            'flip_back_image_width',
            [
                'label'=> esc_html__('Width', 'BEA'),
                'type'=> \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 700,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => 60,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-flip-box .back-image img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'flip_back_image_height',
            [
                'label'=> esc_html__('Height', 'BEA'),
                'type'=> \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 700,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-flip-box .back-image img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'flip_back_image_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .back-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'flip_back_image_padding',
            [
                'label'=> esc_html__('Padding', 'BEA'),
                'type'=> \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-flip-box .back-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();


		//----------------------------------------------- flip box title section-----------------------------------//
        $this->start_controls_section(
            'flip_title_style',
            [
                'label' => esc_html__( 'Title', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'flip_title_tabs'
        );
        //----------------------------------------------- flip box front title tab-----------------------------------//

        $this->start_controls_tab(
            'flip_title_front_tab',
            [
                'label' => esc_html__( 'Front', 'BEA' ),
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'		 => 'flip_front_title_typography',
                'selector'	 => '{{WRAPPER}} .bea-flip-box .front-title',
            ]
        );

        $this->add_control(
            'flip_front_title_color', 
            [
                'label' => esc_html__('Title Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .front-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'flip_front_title_margin',
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
                    '{{WRAPPER}} .bea-flip-box .front-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        //----------------------------------------------- flip box back title tab-----------------------------------//

        $this->start_controls_tab(
            'flip_title_back_tab',
            [
                'label' => esc_html__( 'Back', 'BEA' ),
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'		 => 'flip_front_back_title_typography',
                'selector'	 => '{{WRAPPER}} .bea-flip-box .back-title',
            ]
        );

        $this->add_control(
            'flip_back_title_color', 
            [
                'label' => esc_html__('Title Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .back-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'flip_back_title_margin',
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
                    '{{WRAPPER}}  .bea-flip-box .back-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();



        
		//----------------------------------------------- flip box sub-title section-----------------------------------//
        $this->start_controls_section(
            'flip_sub_title_style',
            [
                'label' => esc_html__( 'Sub Title', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'flip_sub_title_tabs'
        );
        //----------------------------------------------- flip box front sub-title tab-----------------------------------//

        $this->start_controls_tab(
            'flip_sub_title_front_tab',
            [
                'label' => esc_html__( 'Front', 'BEA' ),
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'		 => 'flip_front_sub_title_typography',
                'selector'	 => '{{WRAPPER}} .bea-flip-box .front-sub-title',
            ]
        );

        $this->add_control(
            'flip_front_sub_title_color', 
            [
                'label' => esc_html__('Text Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .front-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'flip_front_sub_title_margin',
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
                    '{{WRAPPER}} .bea-flip-box .front-sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        //----------------------------------------------- flip box back sub-title tab-----------------------------------//

        $this->start_controls_tab(
            'flip_sub-title_back_tab',
            [
                'label' => esc_html__( 'Back', 'BEA' ),
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'		 => 'flip_back_sub_title_typography',
                'selector'	 => '{{WRAPPER}} .bea-flip-box .back-sub-title',
            ]
        );

        $this->add_control(
            'flip_back_sub_title_color', 
            [
                'label' => esc_html__('Text Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .back-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'flip_back_sub_title_margin',
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
                    '{{WRAPPER}}  .bea-flip-box .back-sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();



        //----------------------------------------------- flip box descriptionsection-----------------------------------//
        $this->start_controls_section(
            'flip_description_style',
            [
                'label' => esc_html__( 'Description', 'BEA' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'flip_description_tabs'
        );
        //----------------------------------------------- flip box front descriptiontab-----------------------------------//

        $this->start_controls_tab(
            'flip_description_front_tab',
            [
                'label' => esc_html__( 'Front', 'BEA' ),
            ]
        );
        $this->add_responsive_control(
            'flip_description_front_width',
            [
                'label' => esc_html__( 'Wrapper Width', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-wid-con .bea-flip-box .front-description'    => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'		 => 'flip_front_description_typography',
                'selector'	 => '{{WRAPPER}} .bea-flip-box .front-description',
            ]
        );

        $this->add_control(
            'flip_front_description_color', 
            [
                'label' => esc_html__('Text Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .front-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'flip_front_description_margin',
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
                    '{{WRAPPER}} .bea-flip-box .front-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        //----------------------------------------------- flip box back descriptiontab-----------------------------------//

        $this->start_controls_tab(
            'flip_description_back_tab',
            [
                'label' => esc_html__( 'Back', 'BEA' ),
            ]
        );
        $this->add_responsive_control(
            'flip_description_back_width',
            [
                'label' => esc_html__( 'Wrapper Width', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-wid-con .bea-flip-box .back-description'    => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'		 => 'flip_back_description_typography',
                'selector'	 => '{{WRAPPER}} .bea-flip-box .back-description',
            ]
        );

        $this->add_control(
            'flip_back_description_color', 
            [
                'label' => esc_html__('Text Color', 'BEA'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .back-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'flip_back_description_margin',
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
                    '{{WRAPPER}}  .bea-flip-box .back-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

         //----------------------------------------------- bUTTON style section------------------------------------------//
         $this->start_controls_section(
            'button_section_style',
            [
                'label' => __('Button', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .bea-flip-box .bea-btn .txt',
               
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
                    '{{WRAPPER}} .bea-flip-box .bea-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-flip-box .bea-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .bea-flip-box .bea-btn span' => 'color: {{VALUE}}',
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
                    '{{WRAPPER}} .bea-flip-box .bea-btn i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-flip-box .bea-btn svg path' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button-background',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-flip-box .bea-btn ',
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bea-flip-box .bea-btn',
			]
		);
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .bea-flip-box .bea-btn  ',
            ]
        );
        $this->add_control(
            'button-border-radius',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'default'    => [
                    'unit'     => 'rem',
                    'top'      => 2,
                    'right'    => 2,
                    'bottom'   => 2,
                    'left'     => 2,
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .bea-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'button-padding',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' =>[ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .bea-btn ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'button-margin',
            [
                'label' => esc_html__( 'Margin', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'default'    => [
                    'unit'     => 'px',
                    'top'      => 30,
                    'right'    => 0,
                    'bottom'   => 0,
                    'left'     => 0,
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .bea-btn ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'default' => "#000",
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box:hover .bea-btn span' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'button_icon_color_hover',
            [
                'label' => esc_html__( 'icon Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => "#000",
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box:hover .bea-btn i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-flip-box:hover .bea-btn svg path' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button-background-hover',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-flip-box:hover .bea-btn ',
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .bea-flip-box:hover .bea-btn',
			]
		);
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border_hover',
                'selector' => '{{WRAPPER}} .bea-flip-box:hover .bea-btn  ',
            ]
        );
        $this->add_control(
            'button-border-radius-hover',
            [
                'label' => esc_html__( 'Border Radius', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
              
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box:hover .bea-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'button-padding-hover',
            [
                'label' => esc_html__( 'Padding', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
               
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box:hover .bea-btn ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'button-margin-hover',
            [
                'label' => esc_html__( 'Margin', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box:hover .bea-btn ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();
        $this->start_controls_section(
            'social_links_style_section',
            [
                'label' => __('Social links', 'BEA'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_social_links' => 'yes',
                ],
            ]
        );
        $this->start_controls_tabs(
            'social_links_tabs'
        );
        //------------------------ btn normal Text  style section------------------------//
        

        
        $this->start_controls_tab(
            'social_links_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );
        $this->add_control(
            'social_links-icon-size',
            [
                'label' => esc_html__( 'icon Size', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    "size" =>16,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .bea-social-links i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-flip-box .bea-social-links svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'social_links_icon_color',
            [
                'label' => esc_html__( 'Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => "#000",
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .bea-social-links i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-flip-box .bea-social-links svg path' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_tab();
  
        //------------------------ btn hover Text  style section------------------------//


        $this->start_controls_tab(
            'social_links_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );
        $this->add_control(
            'social_links-icon-size-hover',
            [
                'label' => esc_html__( 'icon Size', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    "size" =>16,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .bea-social-links a:hover i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-flip-box .bea-social-links a:hover svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'social_links_icon_color-hover',
            [
                'label' => esc_html__( 'Color', 'BEA' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => "#000",
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .bea-social-links a:hover i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bea-flip-box .bea-social-links a:hover svg path' => 'fill: {{VALUE}}',
                ],
            ]
        );


        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'social_links_margin_wrapper',
            [
                'label' => esc_html__( 'Wrapper Margin', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .bea-social-links ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'social_links_margin_item',
            [
                'label' => esc_html__( 'Items Margin', 'BEA' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-flip-box .bea-social-links  a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        $flip_class ="";
        $flips_normal_dirction = ['transform-flip-animate','push-animate','flip-3d-animate','slide-animate','flip-animate'];
        if (in_array($settings['flip_box_style'], $flips_normal_dirction)){
            $flip_class = $settings['flip_box_style'] . ' ' . $settings['flip_box_direction'];
        }elseif($settings['flip_box_style'] =="fade-animate"){
            $flip_class = $settings['flip_box_style'];
        }elseif($settings['flip_box_style'] =="zoom-animate"){
            $flip_class = $settings['flip_box_style']. ' ' . $settings['flip_box_zoom_direction'];
        }elseif($settings['flip_box_style'] =="angle-flip"){
            $flip_class = $settings['flip_box_style'] . '-'.$settings['flip_box_angle_direction'];
        }

        ?>
            <div class="bea-wid-con">
                <div class="bea-flip-box <?php echo esc_attr($flip_class);?>">
                    <div class="bea-flip-box-inner">
                        <div class="bea-flip-box-front">
                            <div class="bea-flip-box-inner-wrap">
                                <?php
                                if($settings["front_media_position"]=="top"):
                                    if($settings['flip_front_media_type']== "icon"): ?>
                                        <div class="front-icon">
                                            <?php Icons_Manager::render_icon($settings['flip_front_icon'], ['aria-hidden' => 'true']); ?>
                                        </div>
                                    <?php endif;
                                    if($settings['flip_front_media_type']== "image"):?>
                                        <div class="front-image">
                                            <?php echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'flip_front_thumbnail', 'flip_front_image' );?>
                                        </div>
                                    <?php endif;
                                endif;
                                if(!empty( $settings["flip_front_title"] )): ?>
                                    <<?php Utils::print_validated_html_tag( $settings['flip_front_title_tag'] ); ?> class="front-title">
                                        <?php echo esc_html( $settings["flip_front_title"] ); ?>
                                    </<?php Utils::print_validated_html_tag( $settings['flip_front_title_tag'] ); ?>>
                                <?php endif;
                                if(!empty( $settings["flip_front_sub_title"] )): ?>
                                    <h5 class="front-sub-title"><?php echo esc_html( $settings["flip_front_sub_title"] ); ?></h5>
                                <?php endif; 
                                if(!empty( $settings["flip_front_description"] )): ?>
                                    <h5 class="front-description"><?php echo esc_html( $settings["flip_front_description"] ); ?></h5>
                                <?php endif; 
                                 if($settings["front_media_position"]=="bottom"):
                                    if($settings['flip_front_media_type']== "icon"): ?>
                                        <div class="front-icon">
                                            <?php Icons_Manager::render_icon($settings['flip_front_icon'], ['aria-hidden' => 'true']); ?>
                                        </div>
                                    <?php endif;
                                    if($settings['flip_front_media_type']== "image"):?>
                                        <div class="front-image">
                                            <?php echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'flip_front_thumbnail', 'flip_front_image' );?>
                                        </div>
                                    <?php endif;
                                endif; ?>
                            </div>
                        </div>
                        <div class="bea-flip-box-back ">
                            <div class="bea-flip-box-inner-wrap wrap-back">
                                <div class="bea-flip-box-back-wrap">
                                    <?php
                                    if($settings["back_media_position"]=="top"):
                                        if($settings['flip_back_media_type']== "icon"): ?>
                                            <div class="back-icon">
                                                <?php Icons_Manager::render_icon($settings['flip_back_icon'], ['aria-hidden' => 'true']); ?>
                                            </div>
                                        <?php endif;
                                        if($settings['flip_back_media_type']== "image"):?>
                                            <div class="back-image">
                                                <?php echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'flip_back_thumbnail', 'flip_back_image' );?>
                                            </div>
                                        <?php endif;
                                    endif;
                                    if(!empty( $settings["flip_back_title"] )): ?>
                                        <<?php Utils::print_validated_html_tag( $settings['flip_back_title_tag'] ); ?> class="back-title">
                                            <?php echo esc_html( $settings["flip_back_title"] ); ?>
                                        </<?php Utils::print_validated_html_tag( $settings['flip_back_title_tag'] ); ?>>
                                    <?php endif;
                                    if(!empty( $settings["flip_back_sub_title"] )): ?>
                                        <h5 class="back-sub-title"><?php echo esc_html( $settings["flip_back_sub_title"] ); ?></h5>
                                    <?php endif; 
                                    if(!empty( $settings["flip_back_description"] )): ?>
                                        <h5 class="back-description"><?php echo esc_html( $settings["flip_back_description"] ); ?></h5>
                                    <?php endif; 
                                    if($settings["back_media_position"]=="bottom"):
                                        if($settings['flip_back_media_type']== "icon"): ?>
                                            <div class="back-icon">
                                                <?php Icons_Manager::render_icon($settings['flip_back_icon'], ['aria-hidden' => 'true']); ?>
                                            </div>
                                        <?php endif;
                                        if($settings['flip_back_media_type']== "image"):?>
                                            <div class="back-image">
                                                <?php echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'flip_back_thumbnail', 'flip_back_image' );?>
                                            </div>
                                        <?php endif;
                                    endif; 
                                    if($settings["show_button"]=="yes"):?>
                                        <a href="<?php echo esc_url( $settings["box_link"]['url'] ); ?>" <?php if ( $settings['box_link']['is_external'] ) {echo'target="_blank"';} ?> class="bea-btn <?php  if($settings['animated_button']== "yes"){echo"txt-trans";}?>"> 
                                            <div class="txt" data-text="<?php echo esc_attr( $settings["button_text"] ); ?>">
                                                <span> <?php echo esc_html( $settings["button_text"] ); ?></span>
                                            </div> 
                                            <?php Icons_Manager::render_icon($settings['button_icon'], ['aria-hidden' => 'true']); ?>
                                        </a>
                                    <?php endif;
                                    if($settings["show_social_links"]=="yes"):?>
                                        <div class="bea-social-links">
                                            <?php foreach (  $settings['social_links_list'] as $item ) {?>
                                                <a href="<?php echo esc_url( $item["social_links_icon_link"]['url'] ); ?>"  <?php if ( $item['social_links_icon_link']['is_external'] ) {echo'target="_blank"';} ?>><?php Icons_Manager::render_icon($item['social_links_icon'], ['aria-hidden' => 'true']); ?> </a>
                                            <?php }?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

}

