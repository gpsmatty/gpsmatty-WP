<?php

/* Start the engine */
include_once( get_template_directory() . '/lib/init.php' );

/* Include code to add post templates */
include_once( get_stylesheet_directory() . '/lib/customize.php' );

/* Include code to add post templates */
include_once( get_stylesheet_directory() . '/lib/post_templates.php' );

/* Set Localization */
load_child_theme_textdomain( 'imagely-rebel', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'imagely-rebel' ) );

/* Child theme */
define( 'CHILD_THEME_NAME', 'Imagely Rebel' );
define( 'CHILD_THEME_URL', 'www.imagely.com/wordpress-photography-themes/rebel' );
define( 'CHILD_THEME_VERSION', '1.0.8' );
define( 'IMAGELY_TRANSPARENT_HEADER', true );
define( 'IMAGELY_FLEXHEIGHT', true );
define( 'IMAGELY_FORCE_WIDGETS', false );
define( 'IMAGELY_SLIDESHOW_CONTAINER', '.front-page-1' );
define( 'IMAGELY_FEATURE_VIDEO', false );
define( 'IMAGELY_WIDGET_NUMBER', 4 );

/* Add HTML5 markup structure */
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

/* Add viewport meta tag for mobile browsers */
add_theme_support( 'genesis-responsive-viewport' );

/* Remove site layouts */
genesis_unregister_layout( 'sidebar-content' );
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

/* Remove sidebars */
unregister_sidebar( 'sidebar-alt' );

/* Accessbility (not for menus because superfish interferes with responsive menu) */
add_theme_support( 'genesis-accessibility', array( '404-page', 'headings', 'search-form', 'skip-links' ) );
add_filter( 'genesis_attr_author-archive-description', 'genesis_attributes_screen_reader_class' );

/* Scripts and styles */
add_action( 'wp_enqueue_scripts', 'imagely_scripts_styles' );
function imagely_scripts_styles() {
	
	wp_enqueue_script( 'imagely-responsive-menu', get_stylesheet_directory_uri() . '/js/imagely-responsive-menu.js', array( 'jquery' ), '1.0.0', true );

	if ( is_single() && is_active_sidebar( 'after-entry' ) && ( is_active_widget( false, false, 'featured-page' ) || is_active_widget( false, false, 'featured-post' ) ) ) {

		wp_enqueue_script( 'imagely-featured-widgets', get_stylesheet_directory_uri() . '/js/imagely-after-entry.js', array(), CHILD_THEME_VERSION );
		wp_enqueue_script( 'masonry' );	
			
	}

	$output = array(
		'mainMenu' => __( 'Menu', 'imagely-rebel' ),
		'subMenu'  => __( 'Menu', 'imagely-rebel' ),
	);
	wp_localize_script( 'imagely-responsive-menu', 'ImagelyL10n', $output );

}

add_action( 'wp_enqueue_scripts', 'imagely_fonts' );
function imagely_fonts() {

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Quattrocento:400,700|Oswald:300,400,700', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );
	
}

/* Add support for custom header */
add_theme_support( 'custom-header', array(
	'width'           => 800,
	'height'          => 100,
	'header-selector' => '.site-title a',
	'header-text'     => false,
	'flex-height'     => true,
) );

/* Rename primary and secondary navigation menus */
add_theme_support ( 'genesis-menus' , array ( 'primary' => __( 'Header Menu', 'imagely-rebel' ), 'secondary' => __( 'Footer Menu', 'imagely-rebel' ) ) );

/* Reposition primary navigation menu */
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

/* Remove output of primary navigation right extras */
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

/* Reposition the secondary navigation menu */
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 12 );

/* Reduce the secondary navigation menu to one level depth */
add_filter( 'wp_nav_menu_args', 'imagely_secondary_menu_args' );
function imagely_secondary_menu_args( $args ){

	if( 'secondary' != $args['theme_location'] )
	return $args;

	$args['depth'] = 1;
	return $args;

}

/* Remove navigation meta box */
add_action( 'genesis_theme_settings_metaboxes', 'imagely_remove_genesis_metaboxes' );
function imagely_remove_genesis_metaboxes( $_genesis_theme_settings_pagehook ) {

    remove_meta_box( 'genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main' );

}

/* Remove header right widget area */
unregister_sidebar( 'header-right' );

/* Remove titles from blog, category, and other archive pages */
remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );
remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );
remove_action( 'genesis_before_loop', 'genesis_do_blog_template_heading' );
remove_action( 'genesis_before_loop', 'genesis_do_posts_page_heading' );

/* Modify the entry title text */
function imagely_title( $title ) {
	if( genesis_get_custom_field( 'large_title' ) ) {
		$title = '<span class="imagely-large-text">' . genesis_get_custom_field( 'large_title' ) . '</span><span class="intro">' . $title . '</span>';
	}
	return $title;
}

/* Add entry title filter to posts and pages */
add_action( 'genesis_entry_header', 'imagely_add_title_filter', 1 );
function imagely_add_title_filter() {
	if( is_singular() ) {
		add_filter( 'the_title', 'imagely_title' );
	}
}

/* Remove post and page title filter after entry header */
add_action( 'genesis_entry_header', 'imagely_remove_title_filter', 15 );
function imagely_remove_title_filter() {
	remove_filter( 'the_title', 'imagely_title' );
}

// Edit the read more link text */
add_filter( 'excerpt_more' , 'imagely_read_more_link' );
add_filter( 'get_the_content_more_link' , 'imagely_read_more_link' );
add_filter( 'the_content_more_link' , 'imagely_read_more_link' );
function imagely_read_more_link() {
	return '...<p class="more-link-wrap"><a class="more-link" href="' . get_permalink() . '">' . __( 'Read More' , 'text-domain' ) .'</a></p>';
}

/* Add Imagely featured image sizes */
add_image_size( 'imagely-featured', 1280, 640, TRUE );
add_image_size( 'imagely-square', 640, 640, TRUE );

/* Replace .alignnone with .aligncenter when Align None is selected for featured images */
add_filter( 'genesis_attr_entry-image', 'imagely_replace_image_alignment' );
function imagely_replace_image_alignment( $attributes ) {
	if ( strpos($attributes['class'],'alignleft') === false && strpos($attributes['class'],'alignleft') === false ) {
		$attributes['class'] .= ' aligncenter';
	}
	return $attributes;
}

/* Modify size of the Gravatar in the author box */
add_filter( 'genesis_author_box_gravatar_size', 'imagely_author_box_gravatar' );
function imagely_author_box_gravatar( $size ) {

	return 160;

}

/* Modify size of the Gravatar in the entry comments */
add_filter( 'genesis_comment_list_args', 'imagely_comments_gravatar' );
function imagely_comments_gravatar( $args ) {

	$args['avatar_size'] = 120;
	return $args;

}

/* Remove the entry meta in the entry footer on category pages */
add_action( 'genesis_before_entry', 'imagely_remove_entry_footer' );
function imagely_remove_entry_footer() {

	if ( is_front_page() || is_archive() || is_page_template( 'page_blog.php' ) ) {

		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

	}

}

/* Setup widget counts */
function imagely_count_widgets( $id ) {

	global $sidebars_widgets;

	if ( isset( $sidebars_widgets[ $id ] ) ) {
		return count( $sidebars_widgets[ $id ] );
	}

}

/* Flexible widget classes */
function imagely_widget_area_class( $id ) {

	$count = imagely_count_widgets( $id );

	$class = '';
	
	if( $count == 1 ) {
		$class .= ' widget-full';
	} elseif( $count % 3 == 1 ) {
		$class .= ' widget-thirds';
	} elseif( $count % 4 == 1 ) {
		$class .= ' widget-fourths';
	} elseif( $count % 2 == 0 ) {
		$class .= ' widget-halves uneven';
	} else {	
		$class .= ' widget-halves';
	}
	return $class;
	
}

/* Add support for 3-column footer widget */
add_theme_support( 'genesis-footer-widgets', 3 );

/* Add support for after entry widget */
add_theme_support( 'genesis-after-entry-widget-area' );

/* Register widget areas */
if ( IMAGELY_WIDGET_NUMBER >= 1 ) {
	genesis_register_sidebar( array(
		'id'          => 'front-page-1',
		'name'        => __( 'Front Page 1', 'imagely-rebel' ),
		'description' => __( 'This is the top, "featured" section on the home page. If no widgets are added, it will not show. It is commonly used to add a large feature message and "Learn More" buttons, much like a "Hero" section. For many Imagely themes, this widget also displays a background image or slideshow that can be managed from the Customizer.', 'imagely-rebel' ),
	) );
}
if ( IMAGELY_WIDGET_NUMBER >= 2 ) {
	genesis_register_sidebar( array(
		'id'          => 'front-page-2',
		'name'        => __( 'Front Page 2', 'imagely-rebel' ),
		'description' => __( 'This is the second section on the home page. If no widgets are added, it will not show. While any content can be added, it is designed for text widgets. The layout (number of columns) changes as you add more widgets.', 'imagely-rebel' ),
	) );
}
if ( IMAGELY_WIDGET_NUMBER >= 3 ) {
	genesis_register_sidebar( array(
		'id'          => 'front-page-3',
		'name'        => __( 'Front Page 3', 'imagely-rebel' ),
		'description' => __( 'This is the third section on the home page. If no widgets are added, it will not show. While any content can be added, it is commonly used for the Featured Posts and Featured Page widgets. These widgets require exact configuration, so please see theme documentation if you would like to match the demos.', 'imagely-rebel' ),
	) );
}
if ( IMAGELY_WIDGET_NUMBER >= 4 ) {
	genesis_register_sidebar( array(
		'id'          => 'front-page-4',
		'name'        => __( 'Front Page 4', 'imagely-rebel' ),
		'description' => __( 'This is the fourth section on the home page. If no widgets are added, it will not show. Any content can be added, but most theme demos simply use text widgets.', 'imagely-rebel' ),
	) );
}
if ( IMAGELY_WIDGET_NUMBER >= 5 ) {
	genesis_register_sidebar( array(
		'id'          => 'front-page-5',
		'name'        => __( 'Front Page 5', 'imagely-rebel' ),
		'description' => __( 'This is the fourth section on the home page. If no widgets are added, it will not show. Any content can be added, but most theme demos simply use text widgets.', 'imagely-rebel' ),
	) );
}
if ( IMAGELY_WIDGET_NUMBER >= 6 ) {
	genesis_register_sidebar( array(
		'id'          => 'front-page-6',
		'name'        => __( 'Front Page 6', 'imagely-rebel' ),
		'description' => __( 'This is the fourth section on the home page. If no widgets are added, it will not show. Any content can be added, but most theme demos simply use text widgets.', 'imagely-rebel' ),
	) );
}
if ( IMAGELY_WIDGET_NUMBER >= 7 ) {
	genesis_register_sidebar( array(
		'id'          => 'front-page-7',
		'name'        => __( 'Front Page 7', 'imagely-rebel' ),
		'description' => __( 'This is the fourth section on the home page. If no widgets are added, it will not show. Any content can be added, but most theme demos simply use text widgets.', 'imagely-rebel' ),
	) );
}
if ( IMAGELY_WIDGET_NUMBER >= 8 ) {
	genesis_register_sidebar( array(
		'id'          => 'front-page-8',
		'name'        => __( 'Front Page 8', 'imagely-rebel' ),
		'description' => __( 'This is the eigth section on the home page. If no widgets are added, it will not show. Any content can be added, but most theme demos simply use text widgets.', 'imagely-rebel' ),
	) );
}
if ( IMAGELY_WIDGET_NUMBER >= 9 ) {
	genesis_register_sidebar( array(
		'id'          => 'front-page-9',
		'name'        => __( 'Front Page 9', 'imagely-rebel' ),
		'description' => __( 'This is the ninth section on the home page. If no widgets are added, it will not show. Any content can be added, but most theme demos simply use text widgets.', 'imagely-rebel' ),
	) );
}
if ( IMAGELY_WIDGET_NUMBER >= 10 ) {
	genesis_register_sidebar( array(
		'id'          => 'front-page-10',
		'name'        => __( 'Front Page 10', 'imagely-rebel' ),
		'description' => __( 'This is the tenth section on the home page. If no widgets are added, it will not show. Any content can be added, but most theme demos simply use text widgets.', 'imagely-rebel' ),
	) );
}

/* Set defaults for Genesis themes settings */
add_filter( 'genesis_theme_settings_defaults', 'imagely_theme_defaults' );
function imagely_theme_defaults( $defaults ) {
	$defaults['blog_cat_num']              	= 10;
	$defaults['content_archive']           	= 'full';
	$defaults['content_archive_limit']     	= 150;
	$defaults['content_archive_thumbnail'] 	= 1;
	$defaults['image_size']					= 'imagely-featured';
	$defaults['image_alignment']			= '';
	$defaults['posts_nav']                 	= 'numeric';
	$defaults['site_layout']               	= 'full-width-content';
	$defaults['comments_pages']				= 1;
	return $defaults;
}
add_action( 'after_switch_theme', 'imagely_theme_setting_defaults' );
function imagely_theme_setting_defaults() {
	if( function_exists( 'genesis_update_settings' ) ) {
		genesis_update_settings( array(
			'blog_cat_num'              => 10,	
			'content_archive'           => 'full',
			'content_archive_limit'     => 150,
			'content_archive_thumbnail' => 1,
			'image-size'				=> 'imagely-featured',
			'image-alignment'			=> '',
			'posts_nav'                 => 'numeric',
			'site_layout'               => 'full-width-content',
			'comments_pages'			=> 1
		) );
	} 
	update_option( 'posts_per_page', 10 );
}

/* Simple Social Icon Defaults */
add_filter( 'simple_social_default_styles', 'imagely_social_default_styles' );
function imagely_social_default_styles( $defaults ) {
	$args = array(
		'alignment'              => 'aligncenter',
		'background_color'       => '#000000',
		'background_color_hover' => '#222222',
		'border_radius'          => 4,
		'icon_color'             => '#ffffff',
		'icon_color_hover'       => '#ffffff',
		'size'                   => 40,
		);
		
	$args = wp_parse_args( $args, $defaults );
	return $args;
}

add_action( 'genesis_footer_output', 'imagely_custom_footer', 9 );
function imagely_custom_footer( $output ) {

	$powered_by_imagely = get_theme_mod( 'powered_by_imagely', true );
	
	$output = '<p>&copy; ' . date('Y') . ' &middot; <a href="' . esc_url( home_url( '/' )) . '" rel="home">' . get_bloginfo( 'name' ) . '</a>';

	if ( $powered_by_imagely ) {
		$output .= ' &middot; ' . __( 'Powered by', 'imagely-rebel') . ' <a href="http://www.imagely.com/" rel="nofollow">Imagely</a></p>';
	} else {
		$output .= '</p>';
	}

	return $output;
}
