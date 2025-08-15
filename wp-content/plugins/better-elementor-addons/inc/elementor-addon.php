<?php


	//display taxnonomy
function better_tax_choice() {
	if ( class_exists( 'WooCommerce' ) ) {
		$categories = get_terms('product_cat' );
		$blogs = array();
		$i     = 0;
		foreach ( $categories as $category ) {
			if ( $i == 0 ) {
				$default = $category->name ;
				$i ++;
			}
			$blogs[ $category->term_id ] = $category->name;
		}
		return $blogs;
	};
}

function blog_adv_cat_array($term = 'category') {
    $cats = get_terms( array(
        'taxonomy' => $term,
        'hide_empty' => true
    ));
    $cat_array = array();
    foreach ($cats as $cat) {
        $cat_array[$cat->slug] = $cat->name;
    }
    return $cat_array;
}


function Bea_contact_forms(){
	$formlist = array();
	$forms_args = array( 'posts_per_page' => -1, 'post_type'=> 'wpcf7_contact_form' );
	$forms = get_posts( $forms_args );
	if( $forms ){
		foreach ( $forms as $form ){
			$formlist[$form->ID] = $form->post_title;
		}
	}else{
		$formlist['0'] = __('Form not found', 'swak_plg');
	}
	return $formlist;
}

add_action('elementor/editor/after_enqueue_scripts', function () {
    wp_enqueue_script(
        'better-elementor',
        plugin_dir_url(__DIR__) . 'assets/js/better-elementor.js',
        array('jquery'),
        '1',
        true // in_footer
    );
    wp_enqueue_script(
        'swiper',
        plugin_dir_url(__DIR__) . 'assets/js/swiper.min.js',
        [ 'jquery' ],
        '5.2.0',
        true
    );
    wp_enqueue_script(
        'better-slider',
        plugin_dir_url(__DIR__) . 'assets/js/slider.js',
        ['jquery'],
        '1.0.0',
        true
    );
	wp_enqueue_script(
		'slider-parallax',
        plugin_dir_url(__DIR__) . 'assets/js/slider-parallax.js',
		[ 'jquery','elementor-editor' ],
		'1.0.0',
		true
	);
});


