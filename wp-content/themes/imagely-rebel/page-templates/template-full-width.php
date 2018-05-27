<?php

/**
 * Template Name: Full Width
 **/

/* Add .imagely-no-content body class */
add_filter( 'body_class', 'imagely_full_width_body_class' );	
function imagely_full_width_body_class( $classes ) {
	$classes[] = 'imagely-full-width';
	return $classes;
}

/* Run it all */
genesis();