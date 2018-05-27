jQuery(function( $ ){

	var windowHeight = $( window ).height();

	if ($(window).width() > 640) {

		$( '.site-header' ) .css({'height': windowHeight +'px'});
			
		$( window ).resize(function(){
		
			var windowHeight = $( window ).height();
		
			$( '.site-header' ) .css({'height': windowHeight +'px'});
		
		});

	}

});