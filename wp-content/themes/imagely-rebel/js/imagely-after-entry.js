jQuery(function( $ ){

	//* Only do the rest if featured page or posts widget is active in after entry widget
	
	if ($( '.after-entry .featured-content' ).length) {

		/**
		 * Add CSS classes for styling of Featured Page and Posts
		 * widget based on how the featured image is aligned.
		 */
		
		//* If featured page/posts have featured image aligned right
		$( '.after-entry .featured-content .entry > a.alignright' )
			.parents( '.featured-content' ).addClass( 'featured-align-right' );

		//* If featured page/posts have featured image aligned left
		$( '.after-entry .featured-content .entry > a.alignleft' )
			.parents( '.featured-content' ).addClass( 'featured-align-left' );

		//* If featured page/posts have featured image aligned center
		$( '.after-entry .featured-content .entry > a.aligncenter' )
			.parents( '.featured-content' ).addClass( 'featured-align-center' );
		
		//* If featured page/posts have featured image aligned none 
		$( '.after-entry .featured-content .entry > a.alignnone' )
			.parents( '.featured-content' ).addClass( 'featured-align-none' );

		/**
		 * Initalize Masonry for the appropriate container
		 */
		
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
			imagesLoaded( pagesContainer, function() {
				pagesMsnry = new Masonry( pagesContainer[0], {
				itemSelector: '.featuredpage.featured-align-center',
				gutter: 20
				});
			});
		}

		/**
		 * Adjust the CSS on the .after-entry widget area
		 */
		
		// if ($( '.after-entry .featured-content .entry > a.alignnone' ).length) {
			$( '.after-entry' ).css({
				'width' : '100%',
				'max-width' : '100%',
				'padding-left' : '0',
				'padding-right' : '0'
			});
		// }

	}

});