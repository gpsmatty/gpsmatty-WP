jQuery(function( $ ){

	// Initialize Masonry
	var container = document.querySelector('.content');
	var msnry;
	imagesLoaded( container, function() {
		msnry = new Masonry( container, {
	  		itemSelector: '.entry',
	  		gutter: 20
	  });
	});

	//* Move archive pagination outside current div to prevent masonry overlap
	var $element_to_be_moved = $('.archive-pagination');
	$element_to_be_moved.insertAfter($element_to_be_moved.parent());

});


