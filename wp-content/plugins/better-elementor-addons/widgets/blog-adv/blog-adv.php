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
class Better_Blog_Adv extends Widget_Base
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
        return 'better-blog-adv';
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
        return __('Blog Advanced', 'BEA');
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
        return 'eicon-posts-grid bea-widget-badge';
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


    public function get_keywords() {
		return ['bea', 'post', 'posts', 'grid', 'query', 'blog', 'blog post'];
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

            //----------------------------------------------- blog setting section-----------------------------------//


            $this->start_controls_section(
                'section_settings',
                [
                    'label' => __('Settings', 'BEA'),
                ]
            );
            $this->add_control(
                'blog_posts_column',
                [
                    'label'     => esc_html__( 'Show Posts Per Row', 'BEA' ),
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'options'   => [
                        'col-lg-12 col-md-12'   => esc_html__( '1', 'BEA' ),
                        'col-lg-6 col-md-6'     => esc_html__( '2', 'BEA' ),
                        'col-lg-4 col-md-6'     => esc_html__( '3', 'BEA' ),
                        'col-lg-3 col-md-6'     => esc_html__( '4', 'BEA' ),
                        'col-lg-2 col-md-6'     => esc_html__( '6', 'BEA' ),
                    ],
                    'default'   => 'col-lg-4 col-md-6',
                ]
            );
            $this->add_control(
                'blog_posts_feature_img',
                [
                    'label'     => esc_html__( 'Show Featured Image', 'BEA' ),
                    'type'      => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'  => esc_html__( 'Yes', 'BEA' ),
                    'label_off' => esc_html__( 'No', 'BEA' ),
                    'default'   => 'yes',
                ]
            );

            $this->add_control(
            'blog_posts_Image_position',
                [
                    'label'     => esc_html__( 'Image Position', 'BEA' ),
                    'type'      => \Elementor\Controls_Manager::SELECT,
                    'options'   => [
                        'top' => esc_html__( 'Top', 'BEA' ),
                        'left' => esc_html__( 'Left', 'BEA' ),
                    ],
                    'default'   => 'top',
                    'condition' => [
                            'blog_posts_feature_img'  => 'yes',
                    ],
                ]
            );

            /**
             * Control: Featured Image Size
            */
            $this->add_group_control(
            \Elementor\Group_Control_Image_Size::get_type(),
                [
                    'name'=> 'blog_posts_feature_img_size',
                    'fields_options'    => [
                        'size'  => [
                            'label' => esc_html__( 'Featured Image Size', 'BEA' ),
                        ],
                    ],
                    'exclude'           => [ 'custom' ],
                    'default'           => 'large',
                    'condition'         => [
                        'blog_posts_feature_img'   => 'yes',
                    ],
                ]
            );

        $this->add_control(
            'blog_posts_title',
            [
                'label'     => esc_html__( 'Show Title', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'BEA' ),
                'label_off' => esc_html__( 'No', 'BEA' ),
                'default'   => 'yes',
            ]
        );

        $this->add_control(
            'blog_posts_title_trim',
            [
                'label'     => esc_html__( 'Crop title by word', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => '',
                'condition' => [
                    'blog_posts_title' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'blog_posts_title_trim_end',
            [
                'label' => esc_html__( 'Title crop end', 'BEA' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( '...', 'BEA' ),
                'placeholder' => esc_html__( '[...] // ....Read More', 'BEA' ),
                'condition' => [
                    'blog_posts_title' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'blog_posts_excerpt',
            [
                'label'     => esc_html__( 'Show Excerpt', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'BEA' ),
                'label_off' => esc_html__( 'No', 'BEA' ),
                'default'   => 'yes',
            ]
        );
        $this->add_control(
            'blog_posts_excerpt_trim',
            [
                'label'     => esc_html__( 'Crop excerpt by word', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => '',
                'condition' => [
                    'blog_posts_excerpt' => 'yes',
                ],
            ]
        );
        $this->add_control(
			'blog_posts_excerpt_trim_end',
			[
				'label' => esc_html__( 'excerpt crop end', 'BEA' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '[...]', 'BEA' ),
				'placeholder' => esc_html__( '[...] // ....Read More', 'BEA' ),
                'condition' => [
                    'blog_posts_excerpt' => 'yes',
                ],
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'blog_posts_content_section',
            [
                'label' => esc_html__( 'Query', 'BEA' ),
            ]
        );
 
        $this->add_control(
            'blog_posts_num',
            [
                'label'     => esc_html__( 'Posts Count', 'BEA' ),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 1,
                'max'       => 100,
                'default'   => 3,
            ]
        );
 
        $this->add_control(
            'blog_posts_cats_sort',
            [
                'label'     => esc_html__( 'Filter By Category', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'render_type' => 'template',
                'label_on'  => esc_html__( 'Yes', 'BEA' ),
                'label_off' => esc_html__( 'No', 'BEA' ),
                'default'   => 'no',
            ]
        );

 
        $this->add_control(
            'blog_posts_cats',
            [
                'label' =>esc_html__('Select Categories', 'BEA'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options'   =>blog_adv_cat_array(),
                'label_block' => true,
                'multiple'  => true,
                'condition' => [ 'blog_posts_cats_sort' => 'yes' ]
            ]
        );
       
        $this->add_control(
            'blog_posts_order_by',
            [
                'label'   => esc_html__( 'Order by', 'BEA' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'date'          => esc_html__( 'Date', 'BEA' ),
                    'title'         => esc_html__( 'Title', 'BEA' ),
                    'author'        => esc_html__( 'Author', 'BEA' ),
                    'modified'      => esc_html__( 'Modified', 'BEA' ),
                    'comment_count' => esc_html__( 'Comments', 'BEA' ),
                ],
                'default' => 'date',
            ]
        );
 
        $this->add_control(
            'blog_posts_sort',
            [
                'label'   => esc_html__( 'Order', 'BEA' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'ASC'  => esc_html__( 'ASC', 'BEA' ),
                    'DESC' => esc_html__( 'DESC', 'BEA' ),
                ],
                'default' => 'DESC',
            ]
        );
 
        $this->end_controls_section();
 




        $this->start_controls_section(
            'section_meta',
            [
                'label' => __('Meta', 'BEA'),
            ]
        );

        $this->add_control(
            'blog_posts_meta',
            [
                'label'=> esc_html__( 'Show Meta Data', 'BEA' ),
                'type'=> \Elementor\Controls_Manager::SWITCHER,
                'label_on'=> esc_html__( 'Yes', 'BEA' ),
                'label_off'=> esc_html__( 'No', 'BEA' ),
                'default'=> 'yes',
            ]
        );
        $this->add_control(
         'blog_posts_title_position',
            [
                'label' => esc_html__( 'Meta Position', 'BEA' ),
                'type'  => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'before_title'  => esc_html__( 'Before Title', 'BEA' ),
                    'after_title' => esc_html__( 'After Title', 'BEA' ),
                    'after_content'  => esc_html__( 'After Content', 'BEA' ),
                ],
                'default' => 'before_title',
                'condition' => [
                    'blog_posts_meta' => 'yes',
                ]
            ]
         );
        $this->add_control(
            'blog_posts_meta_select',
            [
                'label'     => esc_html__( 'Meta Data', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::SELECT2,
                'options'   => [
                    'author'=> esc_html__( 'Author', 'BEA' ),
                    'category'=> esc_html__( 'Category', 'BEA' ),
                    'date'=> esc_html__( 'Date', 'BEA' ),
                    'comment'=> esc_html__( 'Comment', 'BEA' ),
                ],
                'multiple' => true,
                'default'   => [
                    'author',
                    'date'
                ],
                'condition' => [
                    'blog_posts_meta' => 'yes',
                ],
            ]
        );
        $this->add_control(
			'blog_posts_meta_separator',
			[
				'label' => esc_html__( 'Item Separator', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'textdomain' ),
					'dot'  => esc_html__( 'Dot', 'textdomain' ),
					'custom' => esc_html__( 'Custom', 'textdomain' ),
				],
				'condition' => [
                    'blog_posts_meta' => 'yes',
                ],
			]
		);
        $this->add_control(
            'blog_posts_meta_separator_text',
            [
                'label' => esc_html__( 'Custom Separator', 'BEA' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( '/', 'BEA' ),
                'placeholder' => esc_html__( '/', 'BEA' ),
                'condition' => [
                    'blog_posts_meta_separator' => 'custom',
                ],
            ]
        );
        $this->add_control(
            'blog_meta_icon_comment',
            [
                'label' => esc_html__( 'Comment Icon', 'BEA' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'far fa-comment',
                    'library' => 'regular',
                ],
                'condition' => [
                    'blog_posts_meta' => 'yes',
                    'blog_posts_meta_select'   => 'comment'
                ],
            ]
        );
        $this->add_control(
            'blog_meta_icon_author',
            [
                'label' => esc_html__( 'author Icon', 'BEA' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'far fa-user',
                    'library' => 'regular',
                ],
                'condition' => [
                    'blog_posts_meta' => 'yes',
                    'blog_posts_meta_select'   => 'author'
                ],
            ]
        );
        $this->add_control(
            'blog_meta_icon_category',
            [
                'label' => esc_html__( 'category Icon', 'BEA' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'far fa-folder-open',
                    'library' => 'regular',
                ],
                'condition' => [
                    'blog_posts_meta' => 'yes',
                    'blog_posts_meta_select'   => 'category'
                ],
            ]
        );
        $this->add_control(
            'blog_meta_icon_date',
            [
                'label' => esc_html__( 'date Icon', 'BEA' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'far fa-calendar-alt',
                    'library' => 'regular',
                ],
                'condition' => [
                    'blog_posts_meta' => 'yes',
                    'blog_posts_meta_select'   => 'date'
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_FLoating_icon',
            [
                'label' => __('FLoating Icon', 'BEA'),
            ]
        );

        $this->add_control(
            'blog_posts_floating',
            [
                'label'=> esc_html__( 'Show FLoating Icon ', 'BEA' ),
                'type'=> \Elementor\Controls_Manager::SWITCHER,
                'label_on'=> esc_html__( 'Yes', 'BEA' ),
                'label_off'=> esc_html__( 'No', 'BEA' ),
                'default'=> 'yes',
            ]
        );
        $this->add_control(
            'blog_posts_floating_icon',
            [
                'label' => esc_html__( 'Icon', 'BEA' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-arrow-right',
                    'library' => 'solid',
                ],
                'condition' => [
                    'blog_posts_floating' => 'yes'
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_read_more',
            [
                'label' => __('Read More', 'BEA'),
            ]
        );

        $this->add_control(
            'blog_posts_read_more',
            [
                'label'     => esc_html__( 'Show Read More', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'BEA' ),
                'label_off' => esc_html__( 'No', 'BEA' ),
                'default'   => '',
            ]
        ); 

        $this->add_control(
            'blog_posts_btn_text',
            [
                'label' =>esc_html__( 'Label', 'BEA' ),
                'type' => Controls_Manager::TEXT,
                'default' =>esc_html__( 'Learn more ', 'BEA' ),
                'placeholder' =>esc_html__( 'Learn more ', 'BEA' ),
                'condition' => [
                    'blog_posts_read_more' => 'yes',
                ],
            ]
        );
 
        $this->add_control(
         'blog_posts_btn_icons__switch',
         [
             'label' => esc_html__('Icon ', 'BEA'),
             'type' => Controls_Manager::SWITCHER,
             'default' => 'yes',
             'label_on' =>esc_html__( 'Yes', 'BEA' ),
             'label_off' =>esc_html__( 'No', 'BEA' ),
             'condition' => [
                'blog_posts_read_more' => 'yes',
            ],
         ]
        );
 
        $this->add_control(
            'blog_posts_btn_icons',
            [
                'label' =>esc_html__( 'Icon', 'BEA' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'blog_posts_btn_icon',
                 'default' => [
                     'value' => '',
                 ],
                'label_block' => true,
                'condition'  => [
                    'blog_posts_read_more' => 'yes',
                    'blog_posts_btn_icons__switch'  => 'yes'
                ]
            ]
        );
        $this->add_control(
            'blog_posts_btn_icon_align',
            [
                'label' =>esc_html__( 'Icon Position', 'BEA' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' =>esc_html__( 'Before', 'BEA' ),
                    'right' =>esc_html__( 'After', 'BEA' ),
                ],
                'condition'  => [
                    'blog_posts_read_more' => 'yes',
                    'blog_posts_btn_icons__switch'  => 'yes'
                ]
            ]
        );
        $this->add_responsive_control(
            'blog_posts_btn_align',
            [
                'label' =>esc_html__( 'Alignment', 'BEA' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left'    => [
                        'title' =>esc_html__( 'Left', 'BEA' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' =>esc_html__( 'Center', 'BEA' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' =>esc_html__( 'Right', 'BEA' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors'=> [
                     '{{WRAPPER}} .bea-blog-adv .btn-warpper' => 'text-align: {{VALUE}};',
                 ],
                'default' => 'left',
                'condition' => [
                    'blog_posts_read_more' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();

        // images Styles
        $this->start_controls_section(
           'blog_posts_wrapper_style',
           [
               'label'     => esc_html__( 'Wrapper', 'BEA' ),
               'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
               
           ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'blog_posts_wrapper_backgoround',
                'label' => esc_html__( 'Background', 'BEA' ),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-blog-adv'
            ]
        );
        $this->add_responsive_control(
            'blog_posts_wrapper_margin',
            [
                'label'      => esc_html__( 'item Margin', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .blog-card' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'blog_posts_item_padding',
            [
                'label'      => esc_html__( 'Item Padding', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    ' {{WRAPPER}} .bea-blog-adv .blog-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
  
        $this->add_responsive_control(
            'blog_posts_wrapper_padding',
            [
                'label'      => esc_html__( 'Wrapper Padding', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    ' {{WRAPPER}} .bea-blog-adv ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
  

        $this->end_controls_section();

        // images Styles
        $this->start_controls_section(
           'blog_posts_image_style',
           [
               'label'     => esc_html__( 'Image', 'BEA' ),
               'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
               'condition' => [
                   'blog_posts_meta' => 'yes',
               ],
           ]
        );
        $this->add_responsive_control(
            'blog_posts_image_height',
            [
                'label' => esc_html__( 'Height', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    "size" =>250,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'blog_posts_image_height_odd',
            [
                'label' => esc_html__( 'Height (Odd Items)', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .row .bea-item:nth-child(odd) .blog-card .img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'blog_posts_image_width',
            [
                'label' => esc_html__( 'Width', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    "size" => 40 ,
                    "unit" => "%" ,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .img' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-blog-adv .info' => 'width:calc(100% - {{SIZE}}{{UNIT}});',
                ],
                'condition' => [
                    'blog_posts_Image_position' => 'left',
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
                    '{{WRAPPER}} .bea-blog-adv .left-img ' => 'align-items: {{VALUE}};',
                ],
                'condition' => [
                    'blog_posts_Image_position' => 'left',
                ],
            ]
        );
        $this->add_responsive_control(
            'blog_posts_image_margin',
            [
                'label'      => esc_html__( 'Margin', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
 
        $this->add_responsive_control(
            'blog_posts_image_padding',
            [
                'label'      => esc_html__( 'Padding', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    ' {{WRAPPER}} .bea-blog-adv .img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->start_controls_tabs(
            'blog_posts_image_tabs'
        );

        $this->start_controls_tab(
            'blog_posts_image_normal',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'imge_box_shadow',
				'selector' => '{{WRAPPER}} .bea-blog-adv .img',
			]
		);
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'blog_posts_image_overlay_normal',
                'label' => esc_html__( 'Background', 'BEA' ),
                'types' => ['classic', 'gradient'],
                'exclude'   => ['image'],
                'fields_options'  => [
                    'background' => [
                        'label' => esc_html__( 'Overlay Background', 'BEA' ),
                    ]
                ],
                'selector' => '{{WRAPPER}} .bea-blog-adv .img::after'
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'      => 'blog_posts_image_border',
                'label'     => esc_html__( 'Border', 'BEA' ),
                'selector'  => '{{WRAPPER}} .bea-blog-adv .img',
            ]
        );
 
        $this->add_responsive_control(
            'blog_posts_image_radius',
            [
                'label'     => esc_html__( 'Border radius', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
 
        $this->end_controls_tab();

        $this->start_controls_tab(
            'blog_posts_image_hover',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'imge_box_shadow_hover',
				'selector' => '{{WRAPPER}} .bea-blog-adv .img:hover',
			]
		);
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'blog_posts_image_overlay_normal_hover',
                'label' => esc_html__( 'Background', 'BEA' ),
                'types' => ['classic', 'gradient'],
                'exclude'   => ['image'],
                'fields_options'  => [
                    'background' => [
                        'label' => esc_html__( 'Overlay Background', 'BEA' ),
                    ]
                ],
                'selector' => '{{WRAPPER}} .bea-blog-adv .img:hover::after'
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'      => 'blog_posts_image_border_hover',
                'label'     => esc_html__( 'Border', 'BEA' ),
                'selector'  => '{{WRAPPER}} .bea-blog-adv .img:hover',
            ]
        );
 
        $this->add_responsive_control(
            'blog_posts_image_radius_hover',
            [
                'label'     => esc_html__( 'Border radius', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .img:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
 
        $this->end_controls_tab();
        $this->end_controls_tabs();


		$this->end_controls_section();

       // Title Styles
       $this->start_controls_section(
        'blog_posts_title_style',
        [
            'label'     => esc_html__( 'Title', 'BEA' ),
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'blog_posts_title' => 'yes',
            ],
        ]
        );
        $this->add_responsive_control(
            'blog_posts_title_alignment',
            [
                'label'   => esc_html__( 'Alignment', 'BEA' ),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left'   => [
                        'title' => esc_html__( 'Left', 'BEA' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => esc_html__( 'Center', 'BEA' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => esc_html__( 'Right', 'BEA' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__( 'justify', 'BEA' ),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'default'   => 'left',
                'devices'   => [ 'desktop', 'tablet', 'mobile' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .title' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'       => 'blog_posts_title_typography',
                'selector'   => '{{WRAPPER}} .bea-blog-adv .title a',
            ]
        );

        $this->start_controls_tabs(
            'blog_posts_title_tabs'
        );

        $this->start_controls_tab(
            'blog_posts_title_normal',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );

        $this->add_control(
            'blog_posts_title_color',
            [
                'label'      => esc_html__( 'Color', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .title a' => 'color: {{VALUE}};'
                ],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'blog_posts_title_text_stroke',
				'selector' => '{{WRAPPER}} .bea-blog-adv .title a',
			]
		);

        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(), [
                'name'       => 'blog_posts_title_shadow',
                'selector'   => '{{WRAPPER}} .bea-blog-adv .title a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'blog_posts_title_hover',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );

        $this->add_control(
            'blog_posts_title_hover_color',
            [
                'label'      => esc_html__( 'Color', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'blog_posts_title_text_stroke_hover',
				'selector' => '{{WRAPPER}} .bea-blog-adv .title a:hovere',
			]
		);
        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(), [
                'name'       => 'blog_posts_title_hover_shadow',
                'selector'   => '{{WRAPPER}} .bea-blog-adv .title a:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'blog_posts_title_hover_shadow_hr',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );

        $this->add_responsive_control(
            'blog_posts_title_padding',
            [
                'label'      => esc_html__( 'Padding', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'default'    => [
                    'unit'     => 'px',
                    'top'      => 10,
                    'right'    => 0,
                    'bottom'   => 30,
                    'left'     => 0,
                    'isLinked' => true,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Title Styles
        $this->start_controls_section(
            'blog_posts_excerpt_style',
            [
                'label'     => esc_html__( 'Excerpt', 'BEA' ),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'blog_posts_excerpt' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'blog_posts_excerpt_alignment',
            [
                'label'   => esc_html__( 'Alignment', 'BEA' ),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left'   => [
                        'title' => esc_html__( 'Left', 'BEA' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => esc_html__( 'Center', 'BEA' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => esc_html__( 'Right', 'BEA' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__( 'justify', 'BEA' ),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'default'   => 'left',
                'devices'   => [ 'desktop', 'tablet', 'mobile' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .excerpt' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
                'name'       => 'blog_posts_excerpt_typography',
                'selector'   => '{{WRAPPER}} .bea-blog-adv .excerpt',
            ]
        );

        $this->start_controls_tabs(
            'blog_posts_excerpt_tabs'
        );

        $this->start_controls_tab(
            'blog_posts_excerpt_normal',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );

        $this->add_control(
            'blog_posts_excerpt_color',
            [
                'label'      => esc_html__( 'Color', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .excerpt' => 'color: {{VALUE}};'
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'blog_posts_excerpt_text_stroke',
                'selector' => '{{WRAPPER}} .bea-blog-adv .excerpt',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(), [
                'name'       => 'blog_posts_excerpt_shadow',
                'selector'   => '{{WRAPPER}} .bea-blog-adv .excerpt',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'blog_posts_excerpt_hover',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );

        $this->add_control(
            'blog_posts_excerpt_hover_color',
            [
                'label'      => esc_html__( 'Color', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .excerpt:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'blog_posts_excerpt_text_stroke_hover',
                'selector' => '{{WRAPPER}} .bea-blog-adv .excerpt:hovere',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(), [
                'name'       => 'blog_posts_excerpt_hover_shadow',
                'selector'   => '{{WRAPPER}} .bea-blog-adv .excerpt:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'blog_posts_excerpt_hover_shadow_hr',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );

        $this->add_responsive_control(
            'blog_posts_excerpt_padding',
            [
                'label'      => esc_html__( 'Padding', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'default'    => [
                    'unit'     => 'px',
                    'top'      => 10,
                    'right'    => 0,
                    'bottom'   => 30,
                    'left'     => 0,
                    'isLinked' => true,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .excerpt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        // Meta Styles
        $this->start_controls_section(
           'blog_posts_meta_style',
           [
               'label'     => esc_html__( 'Meta', 'BEA' ),
               'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
               'condition' => [
                   'blog_posts_meta' => 'yes',
               ],
           ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
            'name'       => 'blog_posts_meta_typography',
            'selector'   => '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item , {{WRAPPER}} .bea-blog-adv .post-meta .meta-item a',
            ]
        );
        $this->add_responsive_control(
            'blog_posts_meta_alignment',
            [
                'label'   => esc_html__( 'Alignment', 'BEA' ),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start'   => [
                        'title' => esc_html__( 'Left', 'BEA' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => esc_html__( 'Center', 'BEA' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'flex-end'   => [
                        'title' => esc_html__( 'Right', 'BEA' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'space-between' => [
                        'title' => esc_html__( 'Space Between', 'BEA' ),
                        'icon'  => 'eicon-justify-space-between-h',
                    ],
                ],
                'default'   => 'left',
                'devices'   => [ 'desktop', 'tablet', 'mobile' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta' => 'justify-content: {{VALUE}};',
                ],
            ]
        );
           
        $this->add_responsive_control(
            'blog_posts_meta_icon_size',
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
                    "size" =>14,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'blog_posts_meta_gap_icon',
            [
                'label' => esc_html__( 'icon Spacing', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    "size" =>5,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta i , {{WRAPPER}} .bea-blog-adv .post-meta svg' => 'padding-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        ); 
        $this->add_responsive_control(
            'blog_posts_meta_gap',
            [
                'label' => esc_html__( 'item Spacing', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    "size" =>10,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );    
          
       
        $this->start_controls_tabs(
            'blog_posts_meta_tabs'
        );

        $this->start_controls_tab(
            'blog_posts_meta_normal',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );
        $this->add_control(
            'blog_posts_mets_color',
            [
                'label'      => esc_html__( 'Color', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item span , {{WRAPPER}} .bea-blog-adv .post-meta .meta-item a , {{WRAPPER}} .bea-blog-adv .post-meta .meta-item i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item svg path ' => 'fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'meta_background',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'meta_border',
				'selector' => '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'meta_box_shadow',
				'selector' => '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item',
			]
		);
      


        $this->end_controls_tab();

        $this->start_controls_tab(
            'blog_posts_meta_hover',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );
        $this->add_control(
            'blog_posts_meta_color_hover',
            [
                'label'      => esc_html__( 'Color', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item:hover span , {{WRAPPER}} .bea-blog-adv .post-meta .meta-item:hover a , {{WRAPPER}} .bea-blog-adv .post-meta .meta-item:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item:hover svg path ' => 'fill: {{VALUE}};',
                ]
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'meta_background_hover',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item:hover',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'meta_border_hover',
				'selector' => '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item:hover',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'meta_box_shadow_hover',
				'selector' => '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item:hover',
			]
		);
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->add_control(
            'blog_posts_meta_hover_shadow_hr',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );
        $this->add_responsive_control(
            'blog_posts_meta_radius',
            [
                'label'     => esc_html__( 'Border radius', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'blog_posts_meta_padding_item',
            [
                'label'      => esc_html__( 'Item Padding', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'blog_posts_meta_padding_wrapper',
            [
                'label'      => esc_html__( 'Wrapper Padding', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        // read btn Styles
         $this->start_controls_section(
             'blog_posts_meta_separator_style',
             [
                'label'     => esc_html__( 'Meta Separator', 'BEA' ),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'blog_posts_meta_separator' => ['custom','dot'],
                ],
             ]
         );
         $this->add_responsive_control(
            'blog_posts_meta_sperataor_size',
            [
                'label' => esc_html__( 'Size', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-custom ' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-dot' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
         $this->add_control(
            'blog_posts_meta_color_sperataor',
            [
                'label'=> esc_html__( 'Color', 'BEA' ),
                'type'=> \Elementor\Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-custom' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bea-blog-adv .post-meta .meta-dot' => 'background-color: {{VALUE}};',
                ]
            ]
        );

        $this->end_controls_section();

    // read btn Styles
        $this->start_controls_section(
            'blog_posts_btn_style',
            [
                'label'     => esc_html__( 'Read More', 'BEA' ),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'blog_posts_read_more' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(), [
            'name'       => 'blog_posts_btn_typography',
            'selector'   => '{{WRAPPER}} .bea-blog-adv .read-btn',
            ]
        );

            
        $this->add_responsive_control(
            'blog_posts_btn_icon_size',
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
                    "size" =>14,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .read-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-blog-adv .read-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'blog_posts_btn_gap_icon',
            [
                'label' => esc_html__( 'icon Spacing', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    "size" =>5,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .read-btn' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );    

        $this->start_controls_tabs(
            'blog_posts_btn_tabs'
        );

        $this->start_controls_tab(
            'blog_posts_btn_normal',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );
        $this->add_control(
            'blog_posts_btn_color',
            [
                'label'=> esc_html__( 'Color', 'BEA' ),
                'type'=> \Elementor\Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .read-btn span , {{WRAPPER}} .bea-blog-adv .read-btn i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bea-blog-adv .read-btn svg path ' => 'fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'btn_background',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-blog-adv .read-btn',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'btn_border',
                'selector' => '{{WRAPPER}} .bea-blog-adv .read-btn',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'btn_box_shadow',
                'selector' => '{{WRAPPER}} .bea-blog-adv .read-btn',
            ]
        );



        $this->end_controls_tab();

        $this->start_controls_tab(
            'blog_posts_btn_hover',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );
        $this->add_control(
            'blog_posts_btn_color_hover',
            [
                'label'      => esc_html__( 'Color', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .read-btn:hover span , {{WRAPPER}} .bea-blog-adv .read-btn:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bea-blog-adv .read-btn:hover svg path ' => 'fill: {{VALUE}};',
                ]
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'btn_background_hover',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-blog-adv .read-btn:hover',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'btn_border_hover',
                'selector' => '{{WRAPPER}} .bea-blog-adv .read-btn:hover',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'btn_box_shadow_hover',
                'selector' => '{{WRAPPER}} .bea-blog-adv .read-btn:hover',
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->add_control(
            'blog_posts_btn_hover_shadow_hr',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );
        $this->add_responsive_control(
            'blog_posts_btn_radius',
            [
                'label'     => esc_html__( 'Border radius', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .read-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'blog_posts_btn_padding',
            [
                'label'      => esc_html__( 'Padding', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .read-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();


        // read btn Styles
        $this->start_controls_section(
            'blog_posts_floating_style',
            [
                'label'     => esc_html__( 'Floating Icon', 'BEA' ),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'blog_posts_floating' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'blog_posts_floating_icon_size',
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
                    "size" =>18,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .floating-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bea-blog-adv .floating-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'blog_posts_floating_icon_warpper',
            [
                'label' => esc_html__( 'icon warpper', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    "size" =>45,
                    "unit" =>"px",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .floating-icon' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );  

        $this->start_controls_tabs(
            'blog_posts_floating_tabs'
        );

        $this->start_controls_tab(
            'blog_posts_floating_normal',
            [
                'label' => esc_html__( 'Normal', 'BEA' ),
            ]
        );
        $this->add_control(
            'blog_posts_floating_color',
            [
                'label'=> esc_html__( 'Color', 'BEA' ),
                'type'=> \Elementor\Controls_Manager::COLOR,
                'default' => "#000",
                'selectors'  => [
                    '{{WRAPPER}}  {{WRAPPER}} .bea-blog-adv .floating-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bea-blog-adv .floating-icon svg path ' => 'fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'floating_background',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-blog-adv .floating-icon',
            ]
        );
        $this->add_responsive_control(
            'blog_posts_floating_rotate',
            [
                'label' => esc_html__( 'Rotate', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'deg' ],
                'range' => [
                    'px' => [
                        'min' => -360,
                        'max' => 360,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    "size" =>-45,
                    "unit" =>"deg",
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .floating-icon i ,  {{WRAPPER}} .bea-blog-adv .floating-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'floating_border',
                'selector' => '{{WRAPPER}} .bea-blog-adv .floating-icon',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'floating_box_shadow',
                'selector' => '{{WRAPPER}} .bea-blog-adv .floating-icon',
            ]
        );
        $this->add_responsive_control(
            'blog_posts_floating_radius',
            [
                'label'     => esc_html__( 'Border radius', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .floating-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_tab();

        $this->start_controls_tab(
            'blog_posts_floating_hover',
            [
                'label' => esc_html__( 'Hover', 'BEA' ),
            ]
        );
        $this->add_control(
            'blog_posts_floating_color_hover',
            [
                'label'      => esc_html__( 'Color', 'BEA' ),
                'type'       => \Elementor\Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .bea-blog-adv .floating-icon:hover span ,  {{WRAPPER}} .bea-blog-adv .floating-icon:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bea-blog-adv .floating-icon:hover svg path ' => 'fill: {{VALUE}};',
                ]
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'floating_background_hover',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bea-blog-adv .floating-icon:hover',
            ]
        );
        $this->add_responsive_control(
            'blog_posts_floating_rotate_hover',
            [
                'label' => esc_html__( 'Rotate', 'BEA' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'deg' ],
                'range' => [
                    'px' => [
                        'min' => -360,
                        'max' => 360,
                        'step' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .floating-icon:hover i ,  {{WRAPPER}} .bea-blog-adv .floating-icon:hover svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'floating_border_hover',
                'selector' => '{{WRAPPER}} .bea-blog-adv .floating-icon:hover',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'floating_box_shadow_hover',
                'selector' => '{{WRAPPER}} .bea-blog-adv .floating-icon:hover',
            ]
        );
        $this->add_responsive_control(
            'blog_posts_floating_radius_hover',
            [
                'label'     => esc_html__( 'Border radius', 'BEA' ),
                'type'      => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%','rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .bea-blog-adv .floating-icon:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        $default    = [
            'post_type' => 'post',
            'orderby'=> array( $settings['blog_posts_order_by'] => $settings['blog_posts_sort'] ),
            'posts_per_page'=> $settings['blog_posts_num'],
            'ignore_sticky_posts' => 1,
        ];
    
        if($settings['blog_posts_cats_sort'] == 'yes' && $settings['blog_posts_cats'] != ''){
            $default['category_name'] = implode(',', $settings['blog_posts_cats']); 
        }
       
        $post_query = new \WP_Query( $default );
      
        ?>
        <div class="bea-blog-adv">
            <div class="row">
                <?php while ( $post_query->have_posts() ) : $post_query->the_post();
                  $meta_data_html = '';
                  $lastElement = end($settings['blog_posts_meta_select']);
                    if ($settings['blog_posts_meta'] =='yes' ):
                        ob_start(); ?>
                        <div class="post-meta">
                            <?php foreach($settings['blog_posts_meta_select'] as $meta): ?>
                                <div class="meta-item">
                                    <?php  \Elementor\Icons_Manager::render_icon( $settings['blog_meta_icon_'.$meta], [ 'aria-hidden' => 'true' ] );?>
                                    <?php if ($meta =="author"){ ?>
                                    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="author-name"><?php the_author_meta('display_name'); ?></a>
                                    <?php } else if ($meta =="comment"){ ?>
                                        <a href="<?php comments_link(); ?>"><?php echo esc_html( get_comments_number() ); ?> </a>
                                    <?php } 
                                    else if ($meta == "category"){ 
                                        the_category(' | '); 
                                    } else if ($meta =="date"){ ?>
                                        <span class="meta-date-text">
                                            <?php echo esc_html( get_the_date() ); ?>
                                        </span>
                                    <?php } ?>
                                </div>  
                                <?php if($settings["blog_posts_meta_separator"]=="dot" && $meta != $lastElement){?>
                                    <div class="meta-dot"></div>
                                <?php  }elseif($settings["blog_posts_meta_separator"]=="custom" && $meta != $lastElement){?>
                                    <div class="meta-custom"><?php echo esc_html($settings["blog_posts_meta_separator_text"]); ?></div>

                                <?php  }?>
                            <?php endforeach; ?>
                        </div>
                        <?php 
                        $meta_data_html .= ob_get_clean();
                    endif;?>
                    <div class="bea-item <?php echo esc_attr($settings['blog_posts_column']);?>">
                        <div class="blog-card <?php if ($settings["blog_posts_Image_position"]=="left"){echo "left-img";}?>">
                            <a href="<?php echo esc_url(the_permalink()); ?>" class="img">
                                <?php if (has_post_thumbnail() && $settings['blog_posts_feature_img']=="yes") { ?>
                                    <img src="<?php the_post_thumbnail_url( esc_attr( $settings['blog_posts_feature_img_size_size'] ) ); ?>" alt="<?php the_title(); ?>">
                               <?php } ?>           
                            </a>
                            <div class="info ">
                                <?php if ($settings['blog_posts_title_position']=="before_title"):?>
                                    <?php echo $meta_data_html;  ?>
                                <?php endif; ?>

                                <?php if ($settings['blog_posts_title']=="yes"):?>
                                    <h5 class="title">
                                        <a href="<?php echo esc_url(the_permalink()); ?>"> 
                                            <?php if($settings["blog_posts_title_trim"] !='' || $settings["blog_posts_title_trim"] > 0):
                                            echo esc_html(wp_trim_words(get_the_title(),$settings["blog_posts_title_trim"] ,$settings["blog_posts_title_trim_end"])); 
                                            else:
                                               echo esc_html(the_title());
                                            endif; ?>
                                        </a>
                                    </h5>
                                <?php endif; ?>

                                <?php if ($settings['blog_posts_title_position']=="after_title"):?>
                                    <?php echo $meta_data_html;  ?>
                                <?php endif; ?>

                                <?php if ($settings['blog_posts_excerpt']=="yes"):?>
                                    <div class="excerpt">
                                        <?php if($settings["blog_posts_excerpt_trim"] !='' || $settings["blog_posts_excerpt_trim"] > 0):
                                        echo esc_html( wp_trim_words(get_the_excerpt(),$settings["blog_posts_excerpt_trim"] ,$settings["blog_posts_excerpt_trim_end"]));
                                        else:
                                            echo esc_html(the_excerpt());
                                        endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($settings['blog_posts_title_position']=="after_content"):?>
                                    <?php echo $meta_data_html;  ?>
                                <?php endif; ?>
                                <div class="btn-warpper">
                                    <a href="<?php echo esc_url(the_permalink()); ?>" class="read-btn">
                                        <?php if($settings['blog_posts_btn_icons__switch'] === 'yes' && $settings['blog_posts_btn_icon_align']=="left"): 
                                                Icons_Manager::render_icon( $settings['blog_posts_btn_icons'], [ 'aria-hidden' => 'true' ] );
                                        endif; ?>
                                        <span><?php echo esc_html( $settings['blog_posts_btn_text'] ); ?></span>
                                        
                                        <?php if($settings['blog_posts_btn_icons__switch'] === 'yes' && $settings['blog_posts_btn_icon_align']=="right"): 
                                                Icons_Manager::render_icon( $settings['blog_posts_btn_icons'], [ 'aria-hidden' => 'true' ] );
                                        endif; ?>
                                    </a>
                                </div>   
                            </div>
                            <?php if( $settings["blog_posts_floating"]=="yes"):?>
                                <div class="floating-icon">
                                    <?php Icons_Manager::render_icon( $settings['blog_posts_floating_icon'], [ 'aria-hidden' => 'true' ] );?>
                                </div>
                            <?php endif;?>

                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata();?>
            </div>
        </div>
       



        <?php
    }

}

