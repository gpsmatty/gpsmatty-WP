<?php
/**
 * Single Post Template: Featured Image
 */

/* Add custom body class */
add_filter( 'body_class', 'imagely_featured_body_class' );
function imagely_featured_body_class( $classes ) {
   	$classes[] = 'imagely-featured-image';
  	return $classes;
}

/* Force full width content */
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

/* Add extra wrap to .entry-header */
add_action('genesis_entry_header', 'imagely_start_entry_header_wrap', 5);
function imagely_start_entry_header_wrap() {
    echo '<div class="wrap"><!-- start entry header .wrap -->';
}

add_action('genesis_entry_header', 'imagely_close_entry_header_wrap', 12);
function imagely_close_entry_header_wrap() {
    echo '</div><!-- end entry header .wrap -->';
}

/* Enqueue backstretch and backstretch set */
add_action( 'wp_enqueue_scripts', 'imagely_featured_template_scripts' );
function imagely_featured_template_scripts() {
	if ( is_singular( array( 'post', 'page' ) ) && has_post_thumbnail() ) {
		wp_enqueue_script( 'imagely-backstretch', get_stylesheet_directory_uri() . '/js/imagely-backstretch.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'imagely-backstretch-set', get_stylesheet_directory_uri() . '/js/imagely-backstretch-set.js' , array( 'jquery', 'imagely-backstretch' ), '1.0.0', true );
	}
}

/* Set the background image on the .entry-header div */
add_action( 'genesis_after_entry', 'imagely_set_background_image' );
function imagely_set_background_image() {
	$image = array( 'src' => has_post_thumbnail() ? genesis_get_image( array( 'format' => 'url' ) ) : '');
	$div = ".content > .entry .entry-header";	
	wp_localize_script( 'imagely-backstretch-set', 'imagelyBackstretchImages', $image );
	wp_localize_script( 'imagely-backstretch-set', 'imagelyBackstretchDiv', $div );
}

genesis();