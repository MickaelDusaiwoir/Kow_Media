( function ( $ ) {

	"use strict";

// -- variables globals

	var userData = {'civilite' : '' ,'nom' : '', 'prenom' : '', 'jour': '', 'mois' : '', 'annee' : '', 'email' : '', 'adresse' : '', 'code_postal' : '', 'ville' : '' },
		key = new Array('civilite', 'nom' , 'prenom', 'jour', 'mois', 'annee', 'email', 'adresse', 'code_postal', 'ville');

// -- methods


    // BuildSelectForm sert à créer les options du Select des jours, des mois et des années.  

	var buildSelectsForm = function () {

		var mois = new Array();

		mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre'];

		for ( var i = 0; i <= 11; i++ ) 
		{
			$('<option></option').attr('value', i + 1).text(mois[i]).appendTo($('#mois'));
		};

		for ( var j = 1; j <= 31; j++ ) 
		{
			$('<option></option').attr('value', j).text(j).appendTo($('#jour'));
		};

		for ( var u = 1920; u <= 2030; u++ ) {
			$('<option></option').attr('value', u).text(u).appendTo($('#annee'));
		};

	}


    // processURL annule le comportement par defaut du lien.
    // Cette fonction prépare également le lien avec les données personnelles de l'utilisateur,
    // en remplacent toutes les chaines de caractères commencent préfixe de % par la valeur lui correspondante.
    // Une fois l'URL créer la personne est redirigée vers la page du concours. 
    // La condition sert à mettre le paramètre représentant le sexe de la personne dans l'URL du concours cible
    // Ce paramètre voit sa valeur varier selon les paramètres attendus par le site exemple ( H|F ou M.|Mme, ....).
	var processURL = function ( e ) 
    {
		var url = $(this).attr('href');
		
		e.preventDefault();
		
		for (var i = 0; i < key.length; i++) 
		{
            if ( fieldName == 'civilite' )
            {
                var regex = /%sexe\(([^"]){0,1}\|([^"]){0,2}\)/;
                var match = regex.exec(url);
                url = url.replace(match[0], match[userData[fieldName]]);
            }
			var fieldName = key[i];			
			url = url.replace('%' + fieldName, userData[fieldName]);
		}
		
		window.location.href = url;
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
		
		document.cookie = "userData=" + dataToString + ";expires=" + expires.toGMTString();
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
					var tmp = document.cookie.split('=');
					
					if ( tmp[0] == 'userData' )
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
			
			$('#'+ fieldName).attr('value', userData[fieldName]);
		} 
	}

	// Fonction s'exécutant dès le chargement de la page

    // On appelle la fonction servant à creer les Select du formulaire.
    // On appelle la fonction servant à remettre les informations dans les champs du formulaire.
    // on met un écouteur d'événement sur chacun des champs à fin de récupéré les valeurs une fois que le champ a perdu le focus.
	$( function () {

		// -- onload routines
		
		buildSelectsForm();
		
		initFormValues();	   

        $('#civilite').on('blur', saveFieldData);
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
		$('a').on("click", processURL);
	} );

}( jQuery ) );
