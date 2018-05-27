<?php

/**
 * 
 * If front page widgets have content, display widgetized front page. Else display default loop.
 * 
 */

/* Remove custom background support if front page has full background body slideshow */
if ( IMAGELY_SLIDESHOW_CONTAINER == 'body' ) {
	remove_theme_support( 'custom-background' );
}

/* Add CSS classes, enqueue scripts, and replace Genesis loop with front page widgets */
add_action( 'genesis_meta', 'imagely_front_page_genesis_meta' );
function imagely_front_page_genesis_meta() {

	$active_sidebars = ( is_active_sidebar( 'front-page-1' ) || is_active_sidebar( 'front-page-2' ) || is_active_sidebar( 'front-page-3' ) || is_active_sidebar( 'front-page-4' ) ? true : false );
	
	if ( IMAGELY_FORCE_WIDGETS == true || $active_sidebars ) {

		/* If forcing widgets but no widget content, apply no-content body class  */
		if ( IMAGELY_FORCE_WIDGETS == true && !$active_sidebars ) {
			add_filter( 'body_class', 'imagely_no_content' );
			function imagely_no_content( $classes ) {
	   			$classes[] = 'imagely-no-content';
	  			return $classes;
			}
		}

		/* Add .front-page body class */
		add_filter( 'body_class', 'imagely_body_class' );
		function imagely_body_class( $classes ) {
   			$classes[] = 'front-page';
  			return $classes;
		}

		/* Force full width content */
		add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

		/* Add .transparent site-header class to themes with transparent site-header */
		add_filter( 'genesis_attr_site-header', 'imagely_transparent_header_class' );
		function imagely_transparent_header_class( $attributes ) {
			if ( IMAGELY_TRANSPARENT_HEADER == true ) {
				$attributes['class'] .= ' transparent';
			}
			return $attributes;
		}

		/* Enqueue only needed scripts and pass any needed variables through wp_localize_script */
		add_action( 'wp_enqueue_scripts', 'imagely_front_page_scripts' );
		function imagely_front_page_scripts() {
			
			/* Set up some variables for conditionals below */
			$has_featured_widgets = ( is_active_widget( false, false, 'featured-page' ) || is_active_widget( false, false, 'featured-post' ) ? true : false );
			
			$has_featured_video = ( IMAGELY_FEATURE_VIDEO ? true : false );

			$is_ios = ( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || stristr($_SERVER['HTTP_USER_AGENT'],'ipod') ) ? true : false;
			
			$is_android = ( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) ? true : false;

			/* Always enqueue the following scripts */
			wp_enqueue_script( 'imagely-front-page', get_stylesheet_directory_uri() . '/js/imagely-front-page.js', array(), CHILD_THEME_VERSION );
			
			wp_enqueue_script( 'localScroll', get_stylesheet_directory_uri() . '/js/jquery.localScroll.min.js', array( 'scrollTo' ), '1.2.8b', true );		
			
			wp_enqueue_script( 'scrollTo', get_stylesheet_directory_uri() . '/js/jquery.scrollTo.min.js', array( 'jquery' ), '1.4.5-beta', true );
			
			/* Send JS variables to the always-enqueued scripts */
			wp_localize_script( 'imagely-front-page', 'transparentHeader', array( "value" => IMAGELY_TRANSPARENT_HEADER ) );			
			
			wp_localize_script( 'imagely-front-page', 'flexHeight', array( "value" => IMAGELY_FLEXHEIGHT ) );

			wp_localize_script( 'imagely-front-page', 'featuredContent', array( "value" => $has_featured_widgets ) );

			wp_localize_script( 'imagely-front-page', 'featureVideo', array( "value" => $has_featured_video ) );

			wp_localize_script( 'imagely-front-page', 'themeFolder', array( "value" => get_stylesheet_directory_uri() ) );

			/* Enqueue masonry and send JS variables if featured page/posts widgets are active */		
			if ( $has_featured_widgets ) wp_enqueue_script( 'masonry' );	

			/* Enqueue video script if theme and device type support feature video */
			if ( $has_featured_video && !$is_ios && !$is_android) {

				if ( get_option( 'imagely-front-video' ) ) {
				
					$video = get_option( 'imagely-front-video', sprintf( '%s/images/front-page.mp4', get_stylesheet_directory_uri() ) );
			
					$video_url = wp_get_attachment_url( $video );
			
				} else {

					delete_option( 'imagely-front-video' );

					$video_url = sprintf( '%s/images/front-page.mp4', get_stylesheet_directory_uri() ); 

				}
 
				wp_enqueue_script( 'imagely-bgvideo', get_stylesheet_directory_uri() . '/js/jquery.vide.min.js', array( 'jquery' ), '0.5.0', true );
				
				wp_localize_script( 'imagely-bgvideo', 'imagelyVideo', $video_url );

			
			} else {

				/* Enqueue backstretch if front-page-1 widget area is active or there's a full body background */
				if ( is_active_sidebar( 'front-page-1' ) || IMAGELY_SLIDESHOW_CONTAINER == 'body' ) {

					/* Don't allow empty options */
					if ( get_option( 'imagely-front-image-1') === '' ) delete_option( 'imagely-front-image-1' );
					
					$image = get_option( 'imagely-front-image-1', sprintf( '%s/images/front-page.jpg', get_stylesheet_directory_uri() ) );
					
					$image2 = get_option( 'imagely-front-image-2' );
					
					$image3 = get_option( 'imagely-front-image-3' );
						
					wp_enqueue_script( 'imagely-backstretch', get_stylesheet_directory_uri() . '/js/imagely-backstretch.js', array( 'jquery' ), '1.0.0' );
							
					wp_enqueue_script( 'imagely-backstretch-set', get_stylesheet_directory_uri() . '/js/imagely-backstretch-set.js' , array( 'jquery', 'imagely-backstretch' ), '1.0.0' );

					/* Prepare the array of images for Backstretch */
					$backstretch_array = array();
				
					if ( ! empty( $image ) ) $backstretch_array["src"] = str_replace( 'http:', '', $image );			
				
					if ( ! empty( $image2 ) ) $backstretch_array["src2"] = str_replace( 'http:', '', $image2 );
				
					if ( ! empty( $image3 ) ) $backstretch_array["src3"] = str_replace( 'http:', '', $image3 );

					/* Set Backstrech container */
					$div = IMAGELY_SLIDESHOW_CONTAINER;

					/* Send JS variables to Backstretch */
					wp_localize_script( 'imagely-backstretch-set', 'imagelyBackstretchImages', $backstretch_array );
					
					wp_localize_script( 'imagely-backstretch-set', 'imagelyBackstretchDiv', $div );
							
				} 
				
			}

		}

		/* Remove breadcrumbs */
		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

		/* Replace default loop with front page widgets */
		remove_action( 'genesis_loop', 'genesis_do_loop' );
		add_action( 'genesis_loop', 'imagely_front_page_widgets' );

	}

}

/* Add the markup for the front page widgets */
function imagely_front_page_widgets() {

	echo '<h2 class="screen-reader-text">' . __( 'Main Content', 'imagely-rebel' ) . '</h2>';

	if ( IMAGELY_WIDGET_NUMBER >= 1 ) {
		genesis_widget_area( 'front-page-1', array(
			'before' => '<div id="front-page-1" class="front-page-1"><div class="widget-area"><div class="wrap">',
			'after'  => '</div></div></div>',
		) );
	}

	if ( IMAGELY_WIDGET_NUMBER >= 2 ) {
		genesis_widget_area( 'front-page-2', array(
			'before' => '<div id="front-page-2" class="front-page-2"><div class="flexible-widgets widget-area' . imagely_widget_area_class( 'front-page-2' ) . '"><div class="wrap">',
			'after'  => '</div></div></div>',
		) );
	}
	
	if ( IMAGELY_WIDGET_NUMBER >= 3 ) {

		/* Add entry-title filter */
		add_filter( 'the_title', 'imagely_title' );

		genesis_widget_area( 'front-page-3', array(
			'before' => '<div id="front-page-3" class="front-page-3"><div class="widget-area"><div class="wrap">',
			'after'  => '</div></div></div>',
		) );
		
		/* Remove entry-title filter */
		remove_filter( 'the_title', 'imagely_title' );
	}

	if ( IMAGELY_WIDGET_NUMBER >= 4 ) {
		genesis_widget_area( 'front-page-4', array(
			'before' => '<div id="front-page-4" class="front-page-4"><div class="flexible-widgets widget-area' . imagely_widget_area_class( 'front-page-4' ) . '"><div class="wrap">',
			'after'  => '</div></div></div>',
		) );
	}

	if ( IMAGELY_WIDGET_NUMBER >= 5 ) {
		genesis_widget_area( 'front-page-5', array(
			'before' => '<div id="front-page-5" class="front-page-5 front-page-bgimage"><div class="widget-area"><div class="wrap">',
			'after'  => '</div></div></div>',
		) );
	}

	if ( IMAGELY_WIDGET_NUMBER >= 6 ) {
		genesis_widget_area( 'front-page-6', array(
			'before' => '<div id="front-page-6" class="front-page-6"><div class="widget-area"><div class="wrap">',
			'after'  => '</div></div></div>',
		) );
	}

	if ( IMAGELY_WIDGET_NUMBER >= 7 ) {
		genesis_widget_area( 'front-page-7', array(
			'before' => '<div id="front-page-7" class="front-page-7 front-page-bgimage"><div class="widget-area"><div class="wrap">',
			'after'  => '</div></div></div>',
		) );
	}

	if ( IMAGELY_WIDGET_NUMBER >= 8 ) {
		genesis_widget_area( 'front-page-8', array(
			'before' => '<div id="front-page-8" class="front-page-8"><div class="widget-area"><div class="wrap">',
			'after'  => '</div></div></div>',
		) );
	}

	if ( IMAGELY_WIDGET_NUMBER >= 9 ) {
		genesis_widget_area( 'front-page-9', array(
			'before' => '<div id="front-page-9" class="front-page-9 front-page-bgimage"><div class="widget-area"><div class="wrap">',
			'after'  => '</div></div></div>',
		) );
	}

	if ( IMAGELY_WIDGET_NUMBER >= 10 ) {
		genesis_widget_area( 'front-page-10', array(
			'before' => '<div id="front-page-10" class="front-page-10"><div class="widget-area"><div class="wrap">',
			'after'  => '</div></div></div>',
		) );
	}

}

/* Run it all */
genesis();
