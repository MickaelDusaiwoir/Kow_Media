( function ( $ ) {

	"use strict";

// -- variables globals

	var userData = {'civilite' : '' ,'nom' : '', 'prenom' : '', 'jour': '', 'mois' : '', 'annee' : '', 'email' : '', 'adresse' : '', 'code_postal' : '', 'ville' : '' },
		key = new Array('civilite', 'nom' , 'prenom', 'jour', 'mois', 'annee', 'email', 'adresse', 'code_postal', 'ville'),
		defaultValue = {'civilite' : 'man' ,'nom' : 'Rambo', 'prenom' : 'John', 'jour': '1', 'mois' : '1', 'annee' : '1985', 'email' : 'john.rambo@exemple.be', 'adresse' : '62 Rue de Lille', 'code_postal' : '75343', 'ville' : 'Paris' },
		settings = {};

	var mois = new Array();
		mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre'];
// -- methods


    // BuildSelectForm sert à créer les options du Select des jours, des mois et des années.  

	var buildSelectsForm = function () {

		for ( var i = 0; i <= 11; i++ ) 
		{
			$('<option></option').attr('value', i + 1).attr('name', mois[i]).text(mois[i]).appendTo($('#mois'));
		};

		for ( var j = 1; j <= 31; j++ ) 
		{
			$('<option></option').attr('value', j).attr('name', j).text(j).appendTo($('#jour'));
		};

		for ( var u = 1920; u <= 2030; u++ ) 
		{
			$('<option></option').attr('value', u).attr('name', u).text(u).appendTo($('#annee'));
		};

	}

    // processURL annule le comportement par defaut du lien.
    
    // Une fois l'URL créer la personne est redirigée vers la page du concours. 
    // La condition sert à mettre le paramètre représentant le sexe de la personne dans l'URL du concours cible
    // Ce paramètre voit sa valeur varier selon les paramètres attendus par le site exemple ( H|F ou M.|Mme, ....).
	var processURL = function ( e ) 
    {
		var url = $(this).attr('href'),
			param;
		
		e.preventDefault();

		if ( userData['nom'] == '' && userData['prenom'] == '' )
		{
			if ( settings['alert_data'] == '1')
				alert('ATTENTION, remplissez le formulaire pour que tous les concours soient complétés AUTOMATIQUEMENT avec vos informations.');
			url = buildRequest(defaultValue, url);
		}
		else
		{
			url = buildRequest(userData, url);
		}		
		
		//window.open(url);
	}

	// Cette fonction prépare le lien avec les données personnelles de l'utilisateur,
    // en remplacent toutes les chaines de caractères commencent par le préfixe % et ayant la valeur lui correspondante.
    // Les paramètre sont l'url et le tableau a parcourir.
	var buildRequest = function ( data, url ) {

		for (var i = 0; i < key.length; i++) 
		{
            var fieldName = key[i]; 

            if ( fieldName == 'civilite' )
            {
                var reg = new RegExp("[( | )]+","g");
                var match = url.split(reg);

                switch( data[fieldName] )
                {
                    case 'woman':
                        url = match[0].replace('%sexe', match[2]);
                        break;

                    case 'miss':
                        url = match[0].replace('%sexe', match.length == 5 ? match[3] : match[2] );
                        break;

                    case 'man':
                    default :
                        url = match[0].replace('%sexe', match[1]);
                        break;
                }             
            }		
            else
            {
                url = url.replace('%' + fieldName, data[fieldName]);
            }		
		}

		return url;
	}


    // SaveFielData récupère toutes les données du formulaire et appelle la fonction update Cookie.
	var saveFieldData = function ( e )
	{
		var fieldName = $(this).attr('name');
		
		if ( fieldName !== '' )
			userData[fieldName] = $(this).val();
		else
			userData[fieldName] = '';
		
		//console.log('var ' + fieldName + ' = ' + userData[fieldName]);
		updateCookie();
	}


    // Update Cookie cree un cookie comportant toutes les informations de l'utilisateur.
    // Tout d'abord on initialise la date d'aujourd'hui à laquelle on rajoutera un an afin d'obtenir la date d'expiration du cookie.
    // On transforme le tableau en chaine de caractère avant de creer le cookie.
	var updateCookie = function ()
	{
		var today = new Date();
		var expires = new Date();
		expires.setTime(today.getTime() + (365*86400));
		
		// array to string 
		var dataToString = JSON.stringify(userData);
		
		document.cookie = "userData=" + dataToString + ";expires=" + expires.toGMTString()+"; path=/";

		progressBar();
	}


    // initialisation du formulaire, on remet les données personnelles de la personne se trouvant dans le cookie 
    // dans le champ qui lui a été attribué.
    // Pour se faire ont parcour le tableau de cookie a la recherche de celui qui nous intéresse ici userData.
    // on le parse afin de récupéré les données avant de les remettre dans le champ grace au tableau de clé. 
	var initFormValues = function ()
	{
		if ( document.cookie )
		{
			var cookies = document.cookie.split(';');
			
			if ( cookies )
			{
				for ( var i = 0; i < cookies.length; i++ )
				{
					var tmp = cookies[i].split('=');

					if ( tmp[0] == "userData" )
					{	
						userData = jQuery.parseJSON(tmp[1]); 						
						break;
					}
					else if ( tmp[0] == " userData")
					{	
						userData = jQuery.parseJSON(tmp[1]); 						
						break;
					}
				}
			}
		}
		
		for ( var i = 0; i < key.length; i++ )
		{
			var fieldName = key[i];
			
			if ( fieldName == 'civilite' )
				$('#' + userData[fieldName]).attr('checked', 'checked');

			if ( fieldName == 'mois')
				$('option[name="' + mois[userData[fieldName] - 1] +'"]').attr('selected', 'selected');

			if ( fieldName == 'jour' )
				$('option[name="' + userData[fieldName] +'"]').attr('selected', 'selected');

			if ( fieldName == 'annee' )
				$('option[name="' + userData[fieldName] +'"]').attr('selected', 'selected');

			if ( userData[fieldName] !== '' )
				$('#'+ fieldName).attr('value', userData[fieldName]);
			else
				$('#'+ fieldName).attr('placeholder', defaultValue[fieldName]);
		} 
	}


	var getSettings = function () 
	{
		var url = document.URL,
			tmp;

		tmp = url.split('?');

		if ( tmp[1] )
		{
			var param = tmp[1].split('&');

			for ( var i = 0; i < param.length; i++ ) 
			{
				var tmp = param[i].split('=');

				settings[tmp[0]] = tmp[1];
			}
		}

		progressBar();
		console.log(settings);
	}

	var progressBar = function () 
	{
		var nbInputText = 0, nbInputSelect = 0, nbInputEmail = 0, nbInputRadio = 0, nbInputs = 0, formComplete = new Array;
		nbInputText = $('#userData').find('input[type="text"]').length;
		nbInputSelect = $('#userData').find('select').length;
		nbInputEmail = $('#userData').find('input[type="email"]').length;
		nbInputRadio = $('#userData').find('input[type="radio"]').length - 2;
		nbInputs = nbInputText + nbInputSelect + nbInputEmail + nbInputRadio;
		
		if ( settings['pb'] == 1 )
		{ 
			if ( document.cookie )
			{
				var cookies = document.cookie.split(';');

				if ( cookies )
				{
					var pourcentage = 0, 
						totalPourcentage = 0;

					pourcentage = 100 / nbInputs;

					for ( var i = 0; i < cookies.length; i++ )
					{
						var tmp = cookies[i].split('=');

						if ( tmp[0] == "userData" || tmp[0] == " userData")
						{	
							formComplete = jQuery.parseJSON(tmp[1]);

							for( var y = 0; y < key.length; y++ ) 
							{
								if( formComplete[key[y]] !== '' )
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

			if( totalPourcentage == '100' ) 
			{
				$('.pourcentage').fadeOut();
				$('#progress progress').fadeOut( function() {
					$('#progress').append('<div class="formCompleted"><strong class="icon-ok">Formulaire complet&nbsp;!</strong><p>Il ne vous reste plus qu\'à choisir <span>vos cadeaux</span></p></div>');
				});
			}
		}
		else
		{
			$('#progress').hide();
		}
	}


	// Fonction s'exécutant dès le chargement de la page

    // On appelle la fonction servant à creer les Select du formulaire.
    // On appelle la fonction servant à remettre les informations dans les champs du formulaire.
    // on met un écouteur d'événement sur chacun des champs à fin de récupéré les valeurs une fois que le champ a perdu le focus ou au clic pour les boutons radio.
	$( function () {

		// -- onload routines
		
		buildSelectsForm();
		
		initFormValues();

		getSettings();	

		if ( settings['mb'] == 1 )
			$('#dialog-message').modal('toggle');

        $('#man').on('click', saveFieldData);
        $('#woman').on('click', saveFieldData);
        $('#miss').on('click', saveFieldData);
		$('#nom').on('blur', saveFieldData);
		$('#prenom').on('blur', saveFieldData);
		$('#jour').on('blur', saveFieldData);
		$('#mois').on('blur', saveFieldData);
		$('#annee').on('blur', saveFieldData);
		$('#email').on('blur', saveFieldData);
		$('#adresse').on('blur', saveFieldData);
		$('#code_postal').on('blur', saveFieldData);
		$('#ville').on('blur', saveFieldData);

		// Si une balise "a" être actionné on appelle la fonction getUrl 
		$('a.btn').on("click", processURL);
	} );

}( jQuery ) );
