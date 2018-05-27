<?php

/**
 * Template Name: Blog Masonry
 */

/* Add custom body class */
add_filter( 'body_class', 'imagely_masonry_body_class' );
function imagely_masonry_body_class( $classes ) {
	$classes[] = 'imagely-masonry';
	return $classes;
}

/* Enqueue masonry and associated scripts */
add_action( 'wp_enqueue_scripts', 'imagely_masonry_template_scripts' );
function imagely_masonry_template_scripts() {
	wp_enqueue_script( 'masonry' );
	wp_enqueue_script( 'masonry-init', get_stylesheet_directory_uri() . '/js/imagely-blog-masonry.js', '', '', true );
}

/* Remove breadcrumbs */
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

/* Force full width content */
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

//* Remove sidebar/content layout
genesis_unregister_layout( 'sidebar-content' );

/* Force content limit regardless of Content Archive theme settings */
add_filter( 'genesis_pre_get_option_content_archive', 'imagely_show_full_content' );
add_filter( 'genesis_pre_get_option_content_archive_limit', 'imagely_content_limit' );
function imagely_show_full_content() {
	return 'full';
}
function imagely_content_limit() {
	return '100';
}

/* Remove author and comment link */
add_filter( 'genesis_post_info', 'imagely_post_info_filter' );
function imagely_post_info_filter($post_info) {
	$post_info = '[post_date] [post_edit]';
	return $post_info;
}

/* Display featured image */
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_header', 'imagely_masonry_image', 9 );
function imagely_masonry_image() {
	$image_args = array(
		'size' => 'large'
	);
	$image = genesis_get_image( $image_args );
	echo '<a rel="bookmark" href="'. get_permalink() .'">'. $image .'</a>';
}

/* Remove the entry footer content */
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

/* Set up custom loop */
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'imagely_masonry_loop' );
function imagely_masonry_loop() {
	$include = genesis_get_option( 'blog_cat' );
	$exclude = genesis_get_option( 'blog_cat_exclude' ) ? explode( ',', str_replace( ' ', '', genesis_get_option( 'blog_cat_exclude' ) ) ) : '';
	$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$query_args = wp_parse_args(
		genesis_get_custom_field( 'query_args' ),
		array(
			'cat'              => $include,
			'category__not_in' => $exclude,
			'showposts'        => genesis_get_option( 'blog_cat_num' ),
			'paged'            => $paged,
		)
	);
	genesis_custom_loop( $query_args );
}

/* Run it all */
genesis();