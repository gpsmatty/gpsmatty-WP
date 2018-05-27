jQuery(function( $ ){

	/**
	 * Resize Front Page 1 widget area to devices screen height.
	 */
	
	if ( flexHeight.value == true ) {

		var windowHeight = ( $(window).width() > 1120 ? $( window ).height() : $( window ).height() - 100);

		windowHeight = windowHeight > 300 ? windowHeight : 300 ;

		$( '.front-page-1' ) .css({'height': windowHeight +'px'});

		$( window ).resize(function(){
			
			var windowHeight = ( $(window).width() > 1120 ? $( window ).height() : $( window ).height() - 100);
			
			windowHeight = windowHeight > 300 ? windowHeight : 300 ;
			
			$( '.front-page-1' ) .css({'height': windowHeight +'px'});

		});

	}

	/**
	 * Control scroll speed on home page. 
	 */
	
	$.localScroll({
		duration: 750
	});


	/**
	 * Remove .transparent CSS class when scrolling. 
	 * This is for themes with transparent, fixed header. 
	 * The .transparent body class is set by PHP in front-page.php.
	 */

	if ( transparentHeader.value == true ) {

		if ( $( document ).scrollTop() > 100 ){
			$( '.site-header' ).removeClass( 'transparent' );	
		}

		$( document ).on('scroll', function(){

			if ( $( document ).scrollTop() > 100 ){
				$( '.site-header' ).removeClass( 'transparent' );	

			} else {
				$( '.site-header' ).addClass( 'transparent' );	
			}

		});
	}

	/**
	 * Add CSS class for styling of Featured Page and Posts
	 * widget based on how the featured image is aligned.
	 */
	
	var isIos = ( (navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) ) ? true : false;
	var isAndroid = ( navigator.userAgent.match(/android/i) ) ? true : false;

	if ( featureVideo.value == true && !isIos && !isAndroid ) {
		$('.front-page-1').vide({
			mp4: imagelyVideo,
			poster: themeFolder + '/images/front-page'
		}, {
			position: '50% 50%',
	  		posterType: 'png',
	  		resizing: true 
		});
		$('.front-page-1').css({ "z-index":"0" });
		$('.front-page-1 video').css({ "height":"100%","width":"auto" });
		$(window).resize( function() {
			$('.front-page-1 video').css({ "height":"100%","width":"auto" });
		});
	}


	/**
	 * Add front-page-bgimage to front-page-3 if a background image is set 
	 */

	if ($('.front-page-3').css('background-image') != 'none') {
		$('.front-page-3').addClass('front-page-bgimage');
	}
	

	/**
	 * Add CSS class for styling of Featured Page and Posts
	 * widget based on how the featured image is aligned.
	 */
	
	if ( featuredContent.value == true ) {

		//* If featured page/posts have featured image aligned right
		$( '.front-page-2 .featured-content .entry > a.alignright, .front-page-3 .featured-content .entry > a.alignright,  .front-page-4 .featured-content .entry > a.alignright, .front-page-5 .featured-content .entry > a.alignright, .front-page-6 .featured-content .entry > a.alignright, .front-page-7 .featured-content .entry > a.alignright, .front-page-8 .featured-content .entry > a.alignright, .front-page-9 .featured-content .entry > a.alignright, .front-page-10 .featured-content .entry > a.alignright' )
			.parents( '.featured-content' ).addClass( 'featured-align-right' );

		//* If featured page/posts have featured image aligned left
		$( '.front-page-2 .featured-content .entry > a.alignleft, .front-page-3 .featured-content .entry > a.alignleft, .front-page-4 .featured-content .entry > a.alignleft, .front-page-5 .featured-content .entry > a.alignleft, .front-page-6 .featured-content .entry > a.alignleft, .front-page-7 .featured-content .entry > a.alignleft, .front-page-8 .featured-content .entry > a.alignleft, .front-page-9 .featured-content .entry > a.alignleft, .front-page-10 .featured-content .entry > a.alignleft' )
			.parents( '.featured-content' ).addClass( 'featured-align-left' );

		//* If featured page/posts have featured image aligned center
		$( '.front-page-2 .featured-content .entry > a.aligncenter, .front-page-3 .featured-content .entry > a.aligncenter, .front-page-4 .featured-content .entry > a.aligncenter, .front-page-5 .featured-content .entry > a.aligncenter, .front-page-6 .featured-content .entry > a.aligncenter, .front-page-7 .featured-content .entry > a.aligncenter, .front-page-8 .featured-content .entry > a.aligncenter, .front-page-9 .featured-content .entry > a.aligncenter, .front-page-10 .featured-content .entry > a.aligncenter' )
			.parents( '.featured-content' ).addClass( 'featured-align-center' );
		
		//* If featured page/posts have featured image aligned none 
		$( '.front-page-2 .featured-content .entry > a.alignnone, .front-page-3 .featured-content .entry > a.alignnone, .front-page-4 .featured-content .entry > a.alignnone, .front-page-5 .featured-content .entry > a.alignnone, .front-page-6 .featured-content .entry > a.alignnone, .front-page-7 .featured-content .entry > a.alignnone, .front-page-8 .featured-content .entry > a.alignnone, .front-page-9 .featured-content .entry > a.alignnone, .front-page-10 .featured-content .entry > a.alignnone' )
			.parents( '.featured-content' ).addClass( 'featured-align-none' );
		//* If featured page/posts have featured image aligned none 
		$( '.front-page-3 .featured-content .entry > a.alignnone' )
			.parents( '.widget-area' ).css({ 'padding' : '0' , 'width' : '100%' });

		//* Initialize Masonry for Featured Posts Widgets If Images Are Aligned Center
		var postsContainer = $('.featuredpost.featured-align-center .widget-wrap');
		var postsMsnry;
		if ( postsContainer.length ) {
	 
		    imagesLoaded( postsContainer, function() {
				postsMsnry = new Masonry( postsContainer[0], {
				itemSelector: '.featuredpost.featured-align-center .entry',
				gutter: 20
				});
			});
		 
		}
		
		//* Initialize Masonry for Featured Pages Widgets If Images Are Aligned Center
		var pagesContainer = $('.featuredpage.featured-align-center').parent();
		var pagesMsnry;
		if ( pagesContainer.length ) {
			pagesContainer.parents( '.front-page-3' ).css({
				'width':'90%',
				'margin': '0 auto',
				'padding' : '100px 0' });
			imagesLoaded( pagesContainer, function() {
				pagesMsnry = new Masonry( pagesContainer[0], {
				itemSelector: '.featuredpage.featured-align-center',
				gutter: 20
				});
			});
		}

	}
	

});