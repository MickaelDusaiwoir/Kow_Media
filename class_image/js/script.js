( function ( $ ) {

    "use strict";

    // -- globals

    var sUrl;

    // -- methods

    var getImage = function ( e ) {

    	var width, 
    		height;

    	e.preventDefault();

    	width = $(window).width();

		height = $(window).height();

		sUrl = $(this).attr('href');
		$('<div id="lightBox"></div>').appendTo('body').on('click', deleteLightBox);  

		$('<img src="'+sUrl+'" />').appendTo('#lightBox').on('click', deleteLightBox); 	

		$('#lightBox').css({'width' : width , 'height' : height });

		$('<p></p>').text(sUrl).appendTo('#lightBox').on('click', function ( e ) {

			e.preventDefault;
			e.stopPropagation();

		});
    };

    var deleteLightBox = function ( e ) {

    	$(this).remove();

    };


	$( function () {

		// -- onload routines

		$('a').on("click", getImage);

	} );

}( jQuery ) );