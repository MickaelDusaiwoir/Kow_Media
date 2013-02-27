( function ( $ ) {

    "use strict";

// -- globals


    var sUrl;


// -- methods

	// Création d'un affichage personalisé 

    var personalDisplay = function ( sUrl ) {

    	// sUrl est l'url de l'image.
    	//var html = '<p> Ton code html </p>';
    	//return html;
    }


    // Récupération de l'URL se trouvant sur le a cliquer

    var getUrl = function ( e ) {

    	// On arrête le comportement par défaut du a
    	// On récupère son attribut bref qui possède le lien de l'image
    	// On appelle la fonction qui va créer le light box en lui passant comme paramètre l'URL de l'image

    	e.preventDefault();    	

		sUrl = $(this).attr('href');

		createLightBox( sUrl );
    };


    // Récupération de la taille de la fenêtre du navigateur

    var getWindowSize = function () {

    	var data = new Array();

    	data[0] = $(window).width();
		data[1] = $(window).height();

		return data;
    };


    // Création de la lightBox et affichage de cette dernière ( Fonction générale )

    var createLightBox = function ( sUrl ) {

    	// création des éléments à afficher
    	// On récupére la taille de la fenêtre
    	// on récupeére le html personnalisé si il y en a un
    	// Ajout du div au body, on lui place un écouteur d'événement afin de savoir quand le supprimer
    	// On donne au div la taille de la fenêtre que l'on a récupérée juste avant en utilisant les propriétés Css width et height
    	// On met une condition pour savoir si il y a un html personnalisé alors on l'affiche sinon on affiche l'affichage par default 
    	// Affichage du loader durant le temps de chargement de l'image
    	// Ajout de la source à l'image on attend que son chargement soit fini avant de l'incorporer au div,
    	// on place également un écouteur d'événement et on retire le loader.  
    	// Ajout de l'URL dans le paragraphe ainsi que d'un écouteur d'événement afin de ne pas déclencher la suppression 
    	// de la light box lorsque l'on sélectionne l'URL, on l'affiche dans le div sous l'image

    	var size 	= new Array(),
    		content = $('<div id="lightBox"></div>'), 
    		image	= $('<img />'),		
    		text 	= $('<p></p>'),
    		loader 	= $('<p id="spinner"><img src="./img/loader.gif" width="48" height="48" />Veuilliez patienter.</p>'),
    		html;
    	
    	size = getWindowSize();

    	content.appendTo('body').on('click', deleteLightBox);

		content.css({'width' : size[0] , 'height' : size[1] });
    	
    	if ( html = personalDisplay( sUrl ) ) 
    	{
    		var htmlObject = $(html);
    		htmlObject.appendTo(content);
    	}
    	else 
    	{
    		loader.appendTo(content);

    		image.attr('src',sUrl).load(function () {

	    		loader.hide();
	    		$(this).appendTo(content).on('click', deleteLightBox);

	    		text.text(sUrl).appendTo(content).on('click', function ( e ) {

					e.preventDefault;
					e.stopPropagation();

				});
	    	});    	   
    	} 	
    };


    // Suppression de la light box grâce au écouteur placer sur le div et l'image

    var deleteLightBox = function ( e ) {

    	$(this).remove();

    };


    // Fonction s'exécutant dès le chargement de la page

	$( function () {

		// -- onload routines

		// Si une balise "a" être actionné on appelle la fonction getUrl 
		$('a').on("click", getUrl);

	} );

}( jQuery ) );