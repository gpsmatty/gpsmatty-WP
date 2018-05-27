jQuery(document).ready(function($) {
	
	/*
	 *	Don't show slideshow if there's a background video. 
	 *	Background video will run if:
	 *	
	 *	  1) featureVideo is set to true
	 *	  2) the backstretch div is .front-page-1
	 *	  3) userAgent is NOT ios (which doesn't support bg video well)
	 *	  4) userAgent is NOT android (which doesn't support bg video well)
	 *
	 *  If any one condition is not true, proceed with slideshow.
	 *  
	 */ 
	
	// var	hasFeatureVideo = featureVideo.value;
	// var isFrontPage1 = imagelyBackstretchDiv == '.front-page-1' ? true : false;
	// var isIos = ( (navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) ) ? true : false;
	// var isAndroid = ( navigator.userAgent.match(/android/i) ) ? true : false;

	// if ( !(hasFeatureVideo && isFrontPage1 && !isIos && !isAndroid) ) {

		// Set up empty array
		var imagelyImages = [];

		// Add each image from Bacstretch object to array
		$.each( imagelyBackstretchImages, function( key, value ) {
			imagelyImages.push(value);
		});

		// Pass the image array to Backstretch
		$(imagelyBackstretchDiv).backstretch( imagelyImages ,{duration:3000,fade:750});

	// }

});