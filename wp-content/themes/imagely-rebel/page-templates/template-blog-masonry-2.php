<?php

/**
 * Template Name: Blog Masonry 2
 */

/* Add custom body class to the head */
add_filter( 'body_class', 'imagely_masonry_2_body_class' );
function imagely_masonry_2_body_class( $classes ) {
	$classes[] = 'imagely-masonry-2';
	return $classes;
}

require( get_stylesheet_directory() . '/page-templates/template-blog-masonry.php');


