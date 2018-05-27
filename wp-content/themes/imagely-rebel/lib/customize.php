<?php

/************************************************ 
 * 
 * WP CUSTOMIZER: Register settings and controls 
 * 
 ************************************************/

add_action( 'customize_register', 'imagely_customizer_register', 16 );
function imagely_customizer_register() {

	global $wp_customize;

	/* Adjust the title and description of the default WordPress "Header Image" section */
	$wp_customize->get_section( 'header_image' )->title = __( 'Logo', 'imagely-rebel' );
	$wp_customize->get_section( 'header_image' )->description = __( 'LOGO SIZE: Although we give *maximum* dimensions below, there is a lot of flexibility. Experiment by uploading and cropping your logo at different sizes, aspect ratios, and with more/less space around the edges until it looks the way you want.', 'imagely-rebel' );
	
	/* Remove layout section from Theme Customizer */
	$wp_customize->remove_section( 'genesis_layout' );

	/* Front Featured Image and Video Controls */
	$wp_customize->add_section( 'imagely-image', array(
		'title'          => __( 'Front Page Imagery', 'imagely-rebel' ),
		'description'    => __( '<p>On this panel, you can upload any imagery for the front page widget areas.</p><p>We recommend horizontal or landscape images that are at least <strong>1920 pixels wide</strong>.</p>', 'imagely-rebel' ),
		'priority'       => 75,
	) );


	if ( IMAGELY_FEATURE_VIDEO ) {
		$wp_customize->add_setting( 'imagely-front-video', array(
			'default'  => sprintf( '%s/images/front-page.mp4', get_stylesheet_directory_uri() ),
			'type'     => 'option',
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( 
			new WP_Customize_Media_Control( 
				$wp_customize, 
				'imagely-front-video-control', array(
		    	'label' => __( 'Upload Front Featured Video', 'imagely-rebel' ),
		    	'description' => __( 'This video will play in the background for the Front Page 1 (featured) widget area. For devices that do not support background video, the Front Page 1 background images below will show instead', 'imagely-rebel' ),
		    	'section' => 'imagely-image',
		    	'mime_type' => 'video',
		    	'settings'    => 'imagely-front-video',
		) ) );
	}
	
	$wp_customize->add_setting( 'imagely-front-image-1', array(
		'default'  => sprintf( '%s/images/front-page.jpg', get_stylesheet_directory_uri() ),
		'type'     => 'option',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'front-featured-image-1',
			array(
				'label'       => __( 'Front Page 1 First Image', 'imagely-rebel' ),
				'description' => __( 'Upload first background image for the Front Page 1 (Featured) widget area. If your theme supports background video (there will be a video upload control above this), this background image will show on any devices that do not support video.', 'imagely-rebel' ),
				'section'     => 'imagely-image',
				'settings'    => 'imagely-front-image-1',
			)
		)
	);

	$wp_customize->add_setting( 'imagely-front-image-2', array(
		'type'     => 'option',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'front-featured-image-2',
			array(
				'label'       => __( 'Front Page 1 Second Image', 'imagely-rebel' ),
				'description' => __( 'Upload second background image for the Front Page 1 (Featured) widget area if you would like a slideshow with multiple images.', 'imagely-rebel' ),
				'section'     => 'imagely-image',
				'settings'    => 'imagely-front-image-2',
			)
		)
	);

	$wp_customize->add_setting( 'imagely-front-image-3', array(
		'type'     => 'option',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'front-featured-image-3',
			array(
				'label'       => __( 'Front Page 1 Third Image', 'imagely-rebel' ),
				'description' => __( 'Upload a third background image for the Front Page 1 (Featured) widget area if you would like a slideshow with multiple images.', 'imagely-rebel' ),
				'section'     => 'imagely-image',
				'settings'    => 'imagely-front-image-3',
			)
		)
	);

	/* We only show other front page widget background if there are more than 4 widget areas */
	if ( IMAGELY_WIDGET_NUMBER > 4 ) {
		$wp_customize->add_setting( 'imagely-front-3', array(
			'default'  => sprintf( '%s/images/front-page-3.jpg', get_stylesheet_directory_uri() ),
			'type'     => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'imagely-front-3-control',
				array(
					'label'       => __( 'Front Page 3 Image', 'imagely-rebel' ),
					'description' => __( 'Upload a background image for the Front Page 3 widget area.', 'imagely-rebel' ),
					'section'     => 'imagely-image',
					'settings'    => 'imagely-front-3',
				)
			)
		);
	}

	if ( IMAGELY_WIDGET_NUMBER >= 5 ) {
		$wp_customize->add_setting( 'imagely-front-5', array(
			'default'  => sprintf( '%s/images/front-page-5.jpg', get_stylesheet_directory_uri() ),
			'type'     => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'imagely-front-5-control',
				array(
					'label'       => __( 'Front Page 5 Background', 'imagely-rebel' ),
					'description' => __( 'Upload a background image for the Front Page 5 widget area.', 'imagely-rebel' ),
					'section'     => 'imagely-image',
					'settings'    => 'imagely-front-5',
				)
			)
		);
	}

	if ( IMAGELY_WIDGET_NUMBER >= 7 ) {
		$wp_customize->add_setting( 'imagely-front-7', array(
			'default'  => sprintf( '%s/images/front-page-7.jpg', get_stylesheet_directory_uri() ),
			'type'     => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'imagely-front-7-control',
				array(
					'label'       => __( 'Front Page 7 Background', 'imagely-rebel' ),
					'description' => __( 'Upload a background image for the Front Page 7 widget area.', 'imagely-rebel' ),
					'section'     => 'imagely-image',
					'settings'    => 'imagely-front-7',
				)
			)
		);
	}

	if ( IMAGELY_WIDGET_NUMBER >= 9 ) {
		$wp_customize->add_setting( 'imagely-front-9', array(
			'default'  => sprintf( '%s/images/front-page-9.jpg', get_stylesheet_directory_uri() ),
			'type'     => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'imagely-front-9-control',
				array(
					'label'       => __( 'Front Page 9 Background', 'imagely-rebel' ),
					'description' => __( 'Upload a background image for the Front Page 9 widget area.', 'imagely-rebel' ),
					'section'     => 'imagely-image',
					'settings'    => 'imagely-front-9',
				)
			)
		);
	}

	/****************************************************************
	 *
	 * COLORS CUSTOMIZATIONS. We provide extensive color controls via
	 * the customizer. Below we create a new panel, Custom Colors, to 
	 * hold all color controls. Within that panel, we create new 
	 * sections for header, content, footer, and other. And then 
	 * within each section, we provide specific controls for each
	 * relevant element.
	 *  
	 ****************************************************************/
	
	/* Move default background color control to background image section */
	$wp_customize->get_control( 'background_color'  )->section   = 'background_image';
	$wp_customize->get_control( 'background_color'  )->description   = 'The background color will show only if there is no background image.';

	/* Add Customize Colors panel to contain all other colors */
	$wp_customize->add_panel( 'custom-colors', array(
        'priority' => 99,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'Customize Colors', 'imagely-rebel' ),
        'description' => __( 'Customize your website colors.', 'imagely-rebel' ),
    ) );

    /* Add Header section to Custom Color Panel */
	$wp_customize->add_section( 'header-colors', array(
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'Header Colors', 'imagely-rebel' ),
        'description' => 'Note: Custom header colors may not apply on your home page if your site has a transparent header area. To see changes, click a different page on the right first.',
        'panel' => 'custom-colors',
    ) );


		if ( CHILD_THEME_NAME != 'Imagely Simplicity' ) {

			/* Header: Background color */
			$wp_customize->add_setting(
				'imagely_header_background',
				array(
					'default'           => '#000000',
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'imagely_header_background_control',
					array(
						'description' => __( 'Change the header background color. If your theme has a header background image, this color may not be visible.', 'imagely-rebel' ),
					    'label'       => __( 'Header Background Color', 'imagely-rebel' ),
					    'section'     => 'header-colors',
					    'settings'    => 'imagely_header_background',
					)
				)
			);
		}

		/* Header: Title color */
		$wp_customize->add_setting(
			'imagely_title_color',
			array(
				'default'           => '#ffffff',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_title_color_control',
				array(
					'description' => __( 'Change the title color.', 'imagely-rebel' ),
				    'label'       => __( 'Title Color', 'imagely-rebel' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_title_color',
				)
			)
		);

		/* Header: Description color */
		$wp_customize->add_setting(
			'imagely_description_color',
			array(
				'default'           => '#dddddd',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_description_color_control',
				array(
					'description' => __( 'Change the description color (if visible).', 'imagely-rebel' ),
				    'label'       => __( 'Site Description Color (if Visible)', 'imagely-rebel' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_description_color',
				)
			)
		);

		if ( CHILD_THEME_NAME != 'Imagely Simplicity' ) {

			/* Header: Menu background color */
			$wp_customize->add_setting(
				'imagely_menu_background',
				array(
					'default'           => '#000000',
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'imagely_menu_background_control',
					array(
						'description' => __( 'Change the menu background color.', 'imagely-rebel' ),
					    'label'       => __( 'Menu Background Color', 'imagely-rebel' ),
					    'section'     => 'header-colors',
					    'settings'    => 'imagely_menu_background',
					)
				)
			);

		}

		/* Header: Menu link color */
		$wp_customize->add_setting(
			'imagely_menu_link',
			array(
				'default'           => '#666666',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_menu_link_control',
				array(
					'description' => __( 'Change the menu link color.', 'imagely-rebel' ),
				    'label'       => __( 'Menu Link Color', 'imagely-rebel' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_menu_link',
				)
			)
		);

		/* Header: Menu link hover color */
		$wp_customize->add_setting(
			'imagely_menu_hover',
			array(
				'default'           => '#ffffff',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_menu_hover_control',
				array(
					'description' => __( 'Change the menu link hover color.', 'imagely-rebel' ),
				    'label'       => __( 'Menu Link Hover Color', 'imagely-rebel' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_menu_hover',
				)
			)
		);

		/* Header: Submenu background color */
		$wp_customize->add_setting(
			'imagely_submenu_background',
			array(
				'default'           => '#111111',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_submenu_background_control',
				array(
					'description' => __( 'Change the submenu background color.', 'imagely-rebel' ),
				    'label'       => __( 'Submenu Background Color', 'imagely-rebel' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_submenu_background',
				)
			)
		);

		/* Header: Submenu link color */
		$wp_customize->add_setting(
			'imagely_submenu_link',
			array(
				'default'           => '#666666',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_submenu_link_control',
				array(
					'description' => __( 'Change the submenu link color.', 'imagely-rebel' ),
				    'label'       => __( 'Submenu Link Color', 'imagely-rebel' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_submenu_link',
				)
			)
		);

		/* Header: Submenu link hover color */
		$wp_customize->add_setting(
			'imagely_submenu_hover',
			array(
				'default'           => '#ffffff',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_submenu_hover_control',
				array(
					'description' => __( 'Change the submenu link hover color.', 'imagely-rebel' ),
				    'label'       => __( 'Submenu Link Hover Color', 'imagely-rebel' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_submenu_hover',
				)
			)
		);


	/* Add Mobile Header section to Custom Color Panel */
	$wp_customize->add_section( 'mobile-header-colors', array(
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'Mobile Header Colors', 'imagely-rebel' ),
        'description' => 'Customize the header colors on smaller mobile devices like phones. To preview the appearance, grab the lower right corner of this browser window and reduce the window width until the responsive header appears.',
        'panel' => 'custom-colors',
    ) );


		if ( CHILD_THEME_NAME != 'Imagely Simplicity' ) { 

	    	/* Mobile Header: Background color */
			$wp_customize->add_setting(
				'imagely_mobile_header_background',
				array(
					'default'           => '#000000',
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);	

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'imagely_mobile_header_background_control',
					array(
						'description' => __( 'Change the header background color on mobile devices.', 'imagely-rebel' ),
					    'label'       => __( 'Mobile Header Background', 'imagely-rebel' ),
					    'section'     => 'mobile-header-colors',
					    'settings'    => 'imagely_mobile_header_background',
					)
				)
			);

		}

		/* Mobile Header: Title color */
		$wp_customize->add_setting(
			'imagely_mobile_title_color',
			array(
				'default'           => '#444444',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_mobile_title_color_control',
				array(
					'description' => __( 'Change the mobile title color.', 'imagely-rebel' ),
				    'label'       => __( 'Mobile Title Color', 'imagely-rebel' ),
				    'section'     => 'mobile-header-colors',
				    'settings'    => 'imagely_mobile_title_color',
				)
			)
		);

		/* Mobile Header: Description color */
		$wp_customize->add_setting(
			'imagely_mobile_description_color',
			array(
				'default'           => '#cccccc',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_mobile_description_color_control',
				array(
					'description' => __( 'Change the site description color on mobile devices (if visible).', 'imagely-rebel' ),
				    'label'       => __( 'Site Description Color (if visible)', 'imagely-rebel' ),
				    'section'     => 'mobile-header-colors',
				    'settings'    => 'imagely_mobile_description_color',
				)
			)
		);
		
		/* Mobile Header: Menu link color */
		$wp_customize->add_setting(
			'imagely_mobile_menu_link',
			array(
				'default'           => '#cccccc',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_mobile_menu_link_control',
				array(
					'description' => __( 'Change the menu/submenu link color.', 'imagely-rebel' ),
				    'label'       => __( 'Menu/Submenu Link Color', 'imagely-rebel' ),
				    'section'     => 'mobile-header-colors',
				    'settings'    => 'imagely_mobile_menu_link',
				)
			)
		);

		/* Mobile Header: Menu hover color */
		$wp_customize->add_setting(
			'imagely_mobile_menu_hover',
			array(
				'default'           => '#ffffff',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_mobile_menu_hover_control',
				array(
					'description' => __( 'Change the menu/submenu hover color.', 'imagely-rebel' ),
				    'label'       => __( 'Menu/Submenu Hover Color', 'imagely-rebel' ),
				    'section'     => 'mobile-header-colors',
				    'settings'    => 'imagely_mobile_menu_hover',
				)
			)
		);


	/* Add Content section to Custom Color Panel */
	$wp_customize->add_section( 'content-colors', array(
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'Content Area Colors', 'imagely-rebel' ),
        'description' => '',
        'panel' => 'custom-colors',
    ) );

		/* Content: Link color */
		$wp_customize->add_setting(
			'imagely_link_color',
			array(
				'default'           => '#aaaaaa',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_link_color_control',
				array(
					'description' => __( 'Change the main link color.', 'imagely-rebel' ),
				    'label'       => __( 'Link Color', 'imagely-rebel' ),
				    'section'     => 'content-colors',
				    'settings'    => 'imagely_link_color',
				)
			)
		);

		/* Content: Link hover color */
		$wp_customize->add_setting(
			'imagely_link_hover',
			array(
				'default'           => '#111111',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_link_hover_control',
				array(
					'description' => __( 'Change the main link hover color.', 'imagely-rebel' ),
				    'label'       => __( 'Link Hover Color', 'imagely-rebel' ),
				    'section'     => 'content-colors',
				    'settings'    => 'imagely_link_hover',
				)
			)
		);

		/* Other: Button color */
		$wp_customize->add_setting(
			'imagely_button_color',
			array(
				'default'           => '#000000',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_button_color_control',
				array(
					'description' => __( 'Change the button color.', 'imagely-rebel' ),
				    'label'       => __( 'Button Color', 'imagely-rebel' ),
				    'section'     => 'content-colors',
				    'settings'    => 'imagely_button_color',
				)
			)
		);

    	/* Other: Button hover color */
		$wp_customize->add_setting(
			'imagely_button_hover',
			array(
				'default'           => '#333333',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_button_hover_control',
				array(
					'description' => __( 'Change the button hover color.', 'imagely-rebel' ),
				    'label'       => __( 'Button Hover Color', 'imagely-rebel' ),
				    'section'     => 'content-colors',
				    'settings'    => 'imagely_button_hover',
				)
			)
		);
	
	/* Add Footer section to Custom Color Panel */
	$wp_customize->add_section( 'footer-colors', array(
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'Footer Colors', 'imagely-rebel' ),
        'description' => '',
        'panel' => 'custom-colors',
    ) );

		/* Footer: Display Imagely in Footer */
		$wp_customize->add_setting( 
			'powered_by_imagely', 
			array(
		    	'default' => 1,
		    	'sanitize_callback' => 'imagely_sanitize_checkbox',
		) );
		 
		$wp_customize->add_control( 
			'powered_by_imagely_control', 
			array(
				'description' => __( 'We always appreciate when users leave this on, but it is your site :).', 'imagely-rebel' ),
			    'label' => __( 'Display "Powered by Imagely" in footer?', 'imagely-rebel'),
			    'type' => 'checkbox',
			    'section' => 'footer-colors',
			    'settings' => 'powered_by_imagely',
		) );

		/* Footer: Background color */
		$wp_customize->add_setting(
			'imagely_footer_background',
			array(
				'default'           => '#000000',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_footer_background_control',
				array(
					'description' => __( 'Change the footer background color.', 'imagely-rebel' ),
				    'label'       => __( 'Footer Background Color', 'imagely-rebel' ),
				    'section'     => 'footer-colors',
				    'settings'    => 'imagely_footer_background',
				)
			)
		);

		/* Footer: Widget title color */
		$wp_customize->add_setting(
			'imagely_footer_title',
			array(
				'default'           => '#ffffff',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_footer_title_control',
				array(
					'description' => __( 'Change the footer title color.', 'imagely-rebel' ),
				    'label'       => __( 'Footer Title Color', 'imagely-rebel' ),
				    'section'     => 'footer-colors',
				    'settings'    => 'imagely_footer_title',
				)
			)
		);

		/* Footer: Text color */
		$wp_customize->add_setting(
			'imagely_footer_text',
			array(
				'default'           => '#aaaaaa',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_footer_text_control',
				array(
					'description' => __( 'Change the footer text color.', 'imagely-rebel' ),
				    'label'       => __( 'Footer Text Color', 'imagely-rebel' ),
				    'section'     => 'footer-colors',
				    'settings'    => 'imagely_footer_text',
				)
			)
		);

		/* Footer: Link color */
		$wp_customize->add_setting(
			'imagely_footer_link',
			array(
				'default'           => '#bbbbbb',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_footer_link_control',
				array(
					'description' => __( 'Change the footer link color.', 'imagely-rebel' ),
				    'label'       => __( 'Footer Link Color', 'imagely-rebel' ),
				    'section'     => 'footer-colors',
				    'settings'    => 'imagely_footer_link',
				)
			)
		);

		/* Footer: Link hover color */
		$wp_customize->add_setting(
			'imagely_footer_hover',
			array(
				'default'           => '#ffffff',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_footer_hover_control',
				array(
					'description' => __( 'Change the footer link hover color.', 'imagely-rebel' ),
				    'label'       => __( 'Footer Link Hover Color', 'imagely-rebel' ),
				    'section'     => 'footer-colors',
				    'settings'    => 'imagely_footer_hover',
				)
			)
		);	

}

/************************************************ 
 * 
 * WP CUSTOMIZER: Build and Output the CSS
 * 
 ************************************************/

add_action( 'wp_enqueue_scripts', 'imagely_css' );
function imagely_css() {

	$handle  = defined( 'CHILD_THEME_NAME' ) && CHILD_THEME_NAME ? sanitize_title_with_dashes( CHILD_THEME_NAME ) : 'child-theme';

		/* Retrieve the colors from customizer */
	$front_page_default_background = sprintf( '%s/images/front-page.jpg', get_stylesheet_directory_uri() );
	$front_page_3_background = get_option( 'imagely-front-3' , sprintf( '%s/images/front-page-3.jpg', get_stylesheet_directory_uri() ) );
	$front_page_5_background = get_option( 'imagely-front-5' , sprintf( '%s/images/front-page-5.jpg', get_stylesheet_directory_uri() ) );
	$front_page_7_background = get_option( 'imagely-front-7' , sprintf( '%s/images/front-page-7.jpg', get_stylesheet_directory_uri() ) );
	$front_page_9_background = get_option( 'imagely-front-9' , sprintf( '%s/images/front-page-9.jpg', get_stylesheet_directory_uri() ) );
	$header_background = get_theme_mod( 'imagely_header_background', '#000000' );
	$title_color = get_theme_mod( 'imagely_title_color', '#ffffff' );
	$description_color = get_theme_mod( 'imagely_description_color', '#dddddd' );
	$menu_background = get_theme_mod( 'imagely_menu_background', '#000000' );
	$menu_link = get_theme_mod( 'imagely_menu_link', '#666666' );
	$menu_hover = get_theme_mod( 'imagely_menu_hover', '#ffffff' );
	$submenu_background = get_theme_mod( 'imagely_submenu_background', '#111111' );
	$submenu_link = get_theme_mod( 'imagely_submenu_link', '#666666' );
	$submenu_hover = get_theme_mod( 'imagely_submenu_hover', '#ffffff' );
	$mobile_header_background = get_theme_mod( 'imagely_mobile_header_background', '#000000' );
	$mobile_title_color = get_theme_mod( 'imagely_mobile_title_color', '#444444' );
	$mobile_description_color = get_theme_mod( 'imagely_mobile_description_color', '#cccccc' );
	$mobile_menu_link = get_theme_mod( 'imagely_mobile_menu_link', '#cccccc' );
	$mobile_menu_hover = get_theme_mod( 'imagely_mobile_menu_hover', '#ffffff' );
	$link_color = get_theme_mod( 'imagely_link_color', '#aaaaaa' );
	$link_hover = get_theme_mod( 'imagely_link_hover', '#111111' );
	$button_color = get_theme_mod( 'imagely_button_color', '#000000' );
	$button_hover = get_theme_mod( 'imagely_button_hover', '#333333' );
	$footer_background = get_theme_mod( 'imagely_footer_background', '#000000' );
	$footer_title = get_theme_mod( 'imagely_footer_title', '#ffffff' );
	$footer_text = get_theme_mod( 'imagely_footer_text', '#aaaaaa' );
	$footer_link = get_theme_mod( 'imagely_footer_link', '#bbbbbb' );
	$footer_hover = get_theme_mod( 'imagely_footer_hover', '#ffffff' );

	/* Build the CSS */
	$css = '';

	if (IMAGELY_WIDGET_NUMBER > 4) {
		$css .= ( $front_page_default_background !== $front_page_3_background && '' !== $front_page_3_background ) ? sprintf( '
			.front-page-3 {background: url("%1$s") no-repeat scroll center; background-size: cover;}
			.front-page-3 .widget {border: none;}' , $front_page_3_background ) : '';
	}

	if (IMAGELY_WIDGET_NUMBER >= 5) {
		$css .= ( $front_page_default_background !== $front_page_5_background && '' !== $front_page_5_background ) ? sprintf( '
			.front-page-5 {background: url("%1$s") no-repeat scroll center; background-size: cover; }' , $front_page_5_background ) : '';
	}
	
	if (IMAGELY_WIDGET_NUMBER >= 7) {	
		$css .= ( $front_page_default_background !== $front_page_7_background && '' !== $front_page_7_background ) ? sprintf( '
			.front-page-7 {background: url("%1$s") no-repeat scroll center; background-size: cover; }' , $front_page_7_background ) : '';
	}
	
	if (IMAGELY_WIDGET_NUMBER >= 9) {
		$css .= ( $front_page_default_background !== $front_page_9_background && '' !== $front_page_9_background ) ? sprintf( '
			.front-page-9 {background: url("%1$s") no-repeat scroll center; background-size: cover; }' , $front_page_9_background ) : '';
	}

	$css .= ( '#000000' !== $header_background ) ? sprintf( '
		.site-header {background-color:  %1$s; border-bottom: none; }' , $header_background ) : '';
	
	$css .= ( '#ffffff' !== $title_color ) ? sprintf( '
		.site-title a, 
		.site-title a:hover {color:  %1$s;}' , $title_color ) : '';
	
	$css .= ( '#dddddd' !== $description_color ) ? sprintf( '
		.site-description {color:  %1$s;}' , $description_color ) : '';
	
	$css .= ( '#000000' !== $menu_background ) ? sprintf( '
		@media screen and (min-width:1024px) {
			.nav-primary {background-color:  %1$s;}
		}' , $menu_background ) : '';
	
	$css .= ( '#666666' !== $menu_link ) ? sprintf( '
		.nav-primary .genesis-nav-menu a {color:  %1$s;}' , $menu_link ) : '';

		if ( CHILD_THEME_NAME == 'Imagely Simplicity' ) {
			$css .= ( '#666666' !== $menu_link ) ? sprintf( '
			.responsive-menu-icon::before {color:  %1$s}' , $menu_link ) : '';
		}
	
	$css .= ( '#ffffff' !== $menu_hover ) ? sprintf( '
		.nav-primary .genesis-nav-menu a:hover, 
		.nav-primary .genesis-nav-menu a:active {color:  %1$s;}' , $menu_hover ) : '';
	
	$css .= ( '#111111' !== $submenu_background ) ? sprintf( '
		@media screen and (min-width:1024px) {
			.genesis-nav-menu .sub-menu {background-color:  %1$s;}
			.genesis-nav-menu .sub-menu a {border-color: %1$s;}
			.genesis-nav-menu > li > ul::before {border-bottom-color: %1$s;}
		}' , $submenu_background ) : '';

	$css .= ( '#666666' !== $submenu_link ) ? sprintf( '
		.nav-primary .genesis-nav-menu .sub-menu a,
		.site-header.transparent .nav-primary .genesis-nav-menu .sub-menu a {color:  %1$s;}' , $submenu_link ) : '';
	
	$css .= ( '#ffffff' !== $submenu_hover ) ? sprintf( '
		.nav-primary .genesis-nav-menu .sub-menu a:hover, 
		.nav-primary .genesis-nav-menu .sub-menu a:active,
		.site-header.transparent .nav-primary .genesis-nav-menu .sub-menu a:hover,
		.site-header.transparent .nav-primary .genesis-nav-menu .sub-menu a:active {color:  %1$s;}' , $submenu_hover ) : '';

	$css .= ( '#000000' !== $mobile_header_background ) ? sprintf( '
		@media only screen and (max-width: 1024px) {
			.site-header,
			.site-header.transparent,
			.nav-primary,
			.nav-primary .genesis-nav-menu .sub-menu,
			.nav-primary .responsive-menu, 
			.nav-primary .responsive-menu .sub-menu {background-color:  %1$s;}
		}' , $mobile_header_background ) : '';

	$css .= ( '#444444' !== $mobile_title_color ) ? sprintf( '
		@media only screen and (max-width: 1024px) {
			.site-title a, 
			.site-title a:hover,
			.site-header.transparent .site-title a,
			.site-header.transparent .site-title a:hover {color:  %1$s;}
		}' , $mobile_title_color ) : '';
	
	$css .= ( '#cccccc' !== $mobile_description_color ) ? sprintf( '
		@media only screen and (max-width: 1024px) {
			.site-description, 
			.site-header.transparent .site-description {color:  %1$s;}
		}' , $mobile_description_color ) : '';

	$css .= ( '#cccccc' !== $mobile_menu_link ) ? sprintf( '
		@media only screen and (max-width: 1024px) {
			.nav-primary .genesis-nav-menu a,
			.nav-primary .genesis-nav-menu .sub-menu a,
			.nav-primary .responsive-men a,
			.nav-primary .current-menu-item > a
			.site-header.transparent .nav-primary .genesis-nav-menu a,
			.site-header.transparent .nav-primary .genesis-nav-menu .sub-menu a,
			.site-header.transparent .responsive-menu a,
			.site-header.transparent .responsive-menu .sub-menu a,
			.responsive-menu-icon::before,
			.nav-primary .responsive-menu .menu-item-has-children::before {color:  %1$s;}
		}' , $mobile_menu_link ) : '';
	
	$css .= ( '#ffffff' !== $mobile_menu_hover ) ? sprintf( '
		@media only screen and (max-width: 1024px) {
			.nav-primary .genesis-nav-menu a:hover, 
			.nav-primary .genesis-nav-menu a:active,
			.nav-primary .genesis-nav-menu .sub-menu a:hover, 
			.nav-primary .genesis-nav-menu .sub-menu a:active,
			.nav-primary .responsive-menu a:hover,
			.nav-primary .responsive-menu .current-menu-item > a,
			.nav-primary .responsive-menu .sub-menu a:hover,
			.nav-primary .responsive-menu .sub-menu a:focus,
			.site-header.transparent .genesis-nav-menu a:hover, 
			.site-header.transparent .genesis-nav-menu a:active, 
			.site-header.transparent .genesis-nav-menu .sub-menu a:hover, 
			.site-header.transparent .genesis-nav-menu .sub-menu a:active {color:  %1$s;}

			.site-header, 
			.site-header.transparent {border-bottom-color: %1$s; }
		}' , $mobile_menu_hover ) : '';

	$css .= ( '#aaaaaa' !== $link_color ) ? sprintf( '
		a,
		.author-box a,
		.archive-description a,
		.sidebar a {color:  %1$s;}' , $link_color ) : '';
	
	$css .= ( '#111111' !== $link_hover ) ? sprintf( '
		a:hover, 
		a:focus,
		.author-box a:hover,
		.author-box a:focus,
		.archive-description a:hover,
		.archive-description a:focus,
		.sidebar a:hover,
		.sidebar a:focus {color:  %1$s;}' , $link_hover ) : '';
	
	$css .= ( '#000000' !== $footer_background ) ? sprintf( '
		.footer-widgets, 
		.site-footer {background-color:  %1$s;}' , $footer_background ) : '';
	
	$css .= ( '#ffffff' !== $footer_title ) ? sprintf( '
		.footer-widgets .widget-title {color:  %1$s;}' , $footer_title ) : '';
	
	$css .= ( '#aaaaaa' !== $footer_text ) ? sprintf( '
		.footer-widgets,
		.footer-widgets p, 
		.site-footer,
		.site-footer p {color:  %1$s;}' , $footer_text ) : '';
	
	$css .= ( '#bbbbbb' !== $footer_link) ? sprintf( '
		.footer-widgets a, 
		.footer-widgets .genesis-nav-menu a,
		.footer-widgets .entry-title a,
		.site-footer a,
		.site-footer .genesis-nav-menu a {color:  %1$s;}' , $footer_link ) : '';
		
	$css .= ( '#ffffff' !== $footer_hover ) ? sprintf( '
		.footer-widgets a:hover, 
		.footer-widgets a:active, 
		.footer-widgets .genesis-nav-menu a:active,
		.footer-widgets .genesis-nav-menu a:hover,
		.footer-widgets .entry-title a:hover,
  		.footer-widgets .entry-title a:focus,
		.site-footer a:hover, 
		.site-footer a:hover,
		.site-footer .genesis-nav-menu a:active,
		.site-footer .genesis-nav-menu a:hover {color:  %1$s;}' , $footer_hover ) : '';
	
	$css .= ( '#000000' !== $button_color ) ? sprintf( '
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.button,
		.content .widget .textwidget a.button,
		.entry-content a.button,
		.entry-content a.more-link,
		.footer-widgets button,
		.footer-widgets input[type="button"],
		.footer-widgets input[type="reset"],
		.footer-widgets input[type="submit"],
		.footer-widgets .button,
		.footer-widgets .entry-content a.more-link,
		.content .front-page-1 .widget a.button,
		.content .front-page-1 .widget .textwidget a.button,
		.front-page-1 button,
		.front-page-1 input[type="button"],
		.front-page-1 input[type="reset"],
		.front-page-1 input[type="submit"],
		.front-page-1 .entry-content a.button,
		.front-page-1 .entry-content a.more-link,
		.nav-primary li.highlight > a,
		.archive-pagination li a:hover,
		.archive-pagination li a:focus,
		.front-page .content .fa {background-color:  %1$s;}' , $button_color ) : '';
	
	$css .= ( '#333333' !== $button_hover ) ? sprintf( '
		button:hover,
		button:focus,
		input:hover[type="button"],
		input:focus[type="button"],
		input:hover[type="reset"],
		input:focus[type="reset"],
		input:hover[type="submit"],
		input:focus[type="submit"],
		.button:hover,
		.button:focus,
		.content .widget .textwidget a.button:hover,
		.content .widget .textwidget a.button:focus,
		.entry-content a.button:hover,
		.entry-content a.button:focus,
		.entry-content a.more-link:hover,
		.entry-content a.more-link:focus,
		.footer-widgets button:hover,
		.footer-widgets button:focus,
		.footer-widgets input:hover[type="button"],
		.footer-widgets input:focus[type="button"],
		.footer-widgets input:hover[type="reset"],
		.footer-widgets input:focus[type="reset"],
		.footer-widgets input:hover[type="submit"],
		.footer-widgets input:focus[type="submit"],
		.footer-widgets .button:hover,
		.footer-widgets .button:focus,
		.footer-widgets .entry-content a.more-link:hover,
		.footer-widgets .entry-content a.more-link:focus,
		.content .front-page-1 .widget a.button:hover,
		.content .front-page-1 .widget a.button:focus,
		.content .front-page-1 .widget .textwidget a.button:hover,
		.content .front-page-1 .widget .textwidget a.button:focus,
		.front-page-1 button:hover,
		.front-page-1 button:focus,
		.front-page-1 input:hover[type="button"],
		.front-page-1 input:focus[type="button"],
		.front-page-1 input:hover[type="reset"],
		.front-page-1 input:focus[type="reset"],
		.front-page-1 input:hover[type="submit"],
		.front-page-1 input:focus[type="submit"],
		.front-page-1 .entry-content a.button:hover,
		.front-page-1 .entry-content a.button:focus,
		.front-page-1 .entry-content a.more-link:hover,
		.front-page-1 .entry-content a.more-link:focus,
		.nav-primary li.highlight > a:hover,
		.nav-primary li.highlight > a:focus,
		.archive-pagination li a,
		.archive-pagination .active a {background-color:  %1$s;}' , $button_hover ) : '';


	/* Output the CSS */
	if( $css ){
		wp_add_inline_style( $handle, $css );
	}

	/* Pass a JS variable to front page JS if bg image is set for front-page-3 */
	if ( $front_page_3_background ) {
		wp_localize_script( 'imagely-front-page', 'front3BgImage', array( "value" => true ) );
	}

}

/* Callback to sanitize checkbox setting */
function imagely_sanitize_checkbox( $checked ) {
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}