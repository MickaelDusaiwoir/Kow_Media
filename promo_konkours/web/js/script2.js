(function($) {
	"use strict";

	var nbInputText = 0, nbInputSelect = 0, nbInputEmail = 0, nbInputRadio = 0, nbInputs = 0, formComplete = new Array;

	var	key = new Array('civilite', 'nom' , 'prenom', 'jour', 'mois', 'annee', 'email', 'adresse', 'code_postal', 'ville');

	nbInputText = $('#userData').find('input[type="text"]').length;
	nbInputSelect = $('#userData').find('select').length;
	nbInputEmail = $('#userData').find('input[type="email"]').length;
	nbInputRadio = $('#userData').find('input[type="radio"]').length - 2;
	nbInputs = nbInputText + nbInputSelect + nbInputEmail + nbInputRadio;

	var progressBar = function(){
		if ( document.cookie )
		{
			var cookies = document.cookie.split(';');
			if ( cookies )
			{
				var pourcentage = 0;
				var totalPourcentage = 0;
				pourcentage = 100 / nbInputs;
				for ( var i = 0; i < cookies.length; i++ )
				{
					var tmp = cookies[i].split('=');
					if ( tmp[0] == "userData" || tmp[0] == " userData")
					{	
						formComplete = jQuery.parseJSON(tmp[1]);
						for(var y = 0; y < key.length; y++) 
						{
							if(formComplete[key[y]] !== '')
							{
								totalPourcentage += pourcentage;
							}
						}
						break;
					}
				}
			}
		}

		$('progress').attr('value', totalPourcentage);
		$('#progress p').text(totalPourcentage + ' %');

		if(totalPourcentage == '100') {
			$('.pourcentage').fadeOut();
			$('#progress progress').fadeOut(function(){
				$('#progress').append('<div class="formCompleted"><strong class="icon-ok">Formulaire complet&nbsp;!</strong><p>Il ne vous reste plus qu\'à choisir <span>vos cadeaux</span></p></div>');
			});
		}
	};

	$(function() {
		/* MODAL BOX d'OUVERTURE DE PAGE */
		$('#dialog-message').modal('toggle');

		progressBar();

		$('#man').on('click', progressBar);
        $('#woman').on('click', progressBar);
        $('#miss').on('click', progressBar);
		$('#nom').on('blur', progressBar);
		$('#prenom').on('blur', progressBar);
		$('#jour').on('blur', progressBar);
		$('#mois').on('blur', progressBar);
		$('#annee').on('blur', progressBar);
		$('#email').on('blur', progressBar);
		$('#adresse').on('blur', progressBar);
		$('#code_postal').on('blur', progressBar);
		$('#ville').on('blur', progressBar);


		

	});
	
}(jQuery));