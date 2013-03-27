function countClick ( id ) {
	jQuery.ajax({
		type: "POST",
		url: url_stats,
		data: {"contest_id" : id},
		success: function (data) {
			console.log(data);
		}
	});
}

( function ( $ ) {

	"use strict";

// -- variables globals

	var userData = {'civilite' : '' ,'nom' : '', 'prenom' : '', 'jour': '', 'mois' : '', 'annee' : '', 'email' : '', 'adresse' : '', 'code_postal' : '', 'ville' : '' },
		key = new Array('civilite', 'nom' , 'prenom', 'jour', 'mois', 'annee', 'email', 'adresse', 'code_postal', 'ville'),
		defaultValue = {'civilite' : 'man' ,'nom' : 'Rambo', 'prenom' : 'John', 'jour': '1', 'mois' : '1', 'annee' : '1985', 'email' : 'john.rambo@exemple.be', 'adresse' : '62 Rue de Lille', 'code_postal' : '75343', 'ville' : 'Paris' };

	var mois = new Array();
		mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre'];

// -- methods

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
		
		window.open(url);
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
		
		if ( $(this).val() !== '' )
		{
			if ( fieldName == 'email' ) 
			{
				var regEmail = new RegExp('^[0-9a-z._-]+@{1}[0-9a-z.-]{2,}[.]{1}[a-z]{2,5}$','i');

				if ( regEmail.test( $(this).val() ) )
				{
					$('#form p.error').remove();
					userData[fieldName] = $(this).val();
				}
				else
				{
					if ( $('#form').find('.error').size() == 0 )
						$('<p></p>').attr('class', 'error').text('Email invalide').css({'margin-bottom':'5px', 'padding': '5px 10px'}).insertAfter('#'+fieldName);
				}
			}
			else
			{
				userData[fieldName] = $(this).val();
			}
		}
		else
		{
			userData[fieldName] = '';	
		}
		
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
			
			if ( fieldName == 'civilite' &&  userData['civilite'] !== ''  )
				$('#' + userData[fieldName]).attr('checked', 'checked');

			if ( fieldName == 'mois' &&  userData['mois'] !== '' )
				$('option[name="' + mois[userData[fieldName] - 1] +'"]').attr('selected', 'selected');
			else
				$('option[name="' + defaultValue[fieldName] +'"]').attr('selected', 'selected');

			if ( fieldName == 'jour' &&  userData['jour'] !== '' )
				$('option[name="' + userData[fieldName] +'"]').attr('selected', 'selected');
			else
				$('option[name="' + defaultValue[fieldName] +'"]').attr('selected', 'selected');

			if ( fieldName == 'annee' &&  userData['annee'] !== '' )
				$('option[name="' + userData[fieldName] +'"]').attr('selected', 'selected');
			else
				$('option[name="' + defaultValue[fieldName] +'"]').attr('selected', 'selected');

			if ( userData[fieldName] !== '' )
				$('#'+ fieldName).attr('value', userData[fieldName]);
			else
				$('#'+ fieldName).attr('placeholder', defaultValue[fieldName]);
		} 
	}


	// Cette fonction permet l'affichage d'une barre de progression si le paramètre pb est égal à 1.
	// Cette dernière calcule le nombre d'input afin de pouvoir déduire la valeur en pourcentage d'une donnée.
	// Elle lit le cookie afin de savoir combien de champs ont été remplis et affiche en conséquence le pourcentage de remplissage du formulaire.
	// Si elle est à 100% on affiche un message et on modifie par la même occasion l'affichage.

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
			$('#progress p span').text(totalPourcentage );

			if( totalPourcentage == '100' ) 
			{
				$('.pourcentage').fadeOut();
				$('#progress progress').fadeOut( function() {
					$('#progress strong').text('Etape 1: Formulaire complet à 100% !').removeClass('current icon-cancel').addClass('icon-ok');
					$('#progress small').addClass('current');
				});
			}
			else
			{
				$('.pourcentage').fadeIn();
				$('#progress progress').fadeIn(function() {
					$('#progress strong').text('Etape 1: ').addClass('current icon-cancel').removeClass('icon-ok');
					$('#progress small').removeClass('current');
				});
			}
		}
		else
		{
			$('#progress').hide();
		}
	}


	// Cette fonction permet de choisir quel message où Css on souhaite afficher.
	// Chacun des paramètres possède un switch qui lui permet de délivrer une valeur différente selon la valeur du paramètre.

	var putSettings = function () {

		var url_css;
		url_css = $('#design_css').attr('href');

		switch ( settings['css'] )
		{
			case 1:
				url_css = url_css.replace('style.css', 'style2.css');
				$('#design_css').attr('href', url_css);
				break;

			case 2:
				url_css = url_css.replace('style.css', 'style3.css');
				$('#design_css').attr('href', url_css);
				break;

			case 0:
			default :
				break;
		}
		
		switch ( settings['btn'] )
		{
			case 1:
				$('.astuces .btn').text('Je participe !');
				break;

			case 2:
				$('.astuces .btn').text('Je gagne !');
				break;

			case 0:
			default:
				break;
		}

		switch ( settings['title1'] )
		{
			case 1:
				$('#userData h2 span').text('Remplissez');
				break;

			case 2:
				$('#userData h2 span').text('Répondez');
				break;

			case 0:
			default:
				break;
		}

		switch ( settings['title2'] )
		{
			case 1:
				$('#contests h2 span').text('Participez');
				break;

			case 2:
				$('#contests h2 span').text('Gagnez');
				break;

			case 3:
				$('#contests h2 span').text('Choisissez');
				break;

			case 4:
				$('#contests h2 span').text('Concourez');
				break;

			case 0:
			default:
				break;
		}

		switch ( settings['after_title1'] )
		{
			case 1:
				$('#userData h2 em').append(' et jouez');
				break;

			case 2:
				$('#userData h2 em').append(' et gagnez');
				break;

			case 3:
				$('#userData h2 em').append(' et amusez vous');
				break;

			case 0:
			default:
				break;
		}	

		switch ( settings['after_title2'] )
		{
			case 1:
				$('#contests h2 em').append(' et gagnez');
				break;

			case 2:
				$('#contests h2 em').append(' c\'est gratuit');
				break;

			case 3:
				$('#contests h2 em').append(' tentez votre chance');
				break;

			case 0:
			default:
				break;
		}			

		switch ( settings['pub'] ) 
		{
			case 1:
				$('.pub').attr('class', 'no_show');
				$('#pub_1').attr('class','pub show');
				break;

			case 2:
				$('.pub').attr('class', 'no_show');
				$('#pub_2').attr('class','pub show');
				break;

			case 3:
				$('.pub').attr('class', 'no_show');
				$('#pub_3').attr('class','pub show');
				break;

			case 0:
				$('.pub').attr('class', 'no_show');
				break;
				
			default:			
				break;
		}
	}


	// Desinne le graphique des statistiques.
	var drawGraphicStats = function ( e )
	{

		Highcharts.visualize = function(table, options) {
            // the categories
            options.xAxis.categories = [];
            $('tbody th', table).each( function(i) {
                options.xAxis.categories.push(this.innerHTML);
            });
    
            // the data series
            options.series = [];
            $('tr', table).each( function(i) {
                var tr = this;
                $('th, td', tr).each( function(j) {
                    if (j > 0) { // skip first column
                        if (i == 0) { // get the name and init the series
                            options.series[j - 1] = {
                                name: this.innerHTML,
                                data: []
                            };
                        } else { // add values
                            options.series[j - 1].data.push(parseFloat(this.innerHTML));
                        }
                    }
                });
            });
    
            $('#container').highcharts(options);
        }
    
        var table = $('#datatable'),
        options = {
            /*chart: {
                type: 'column'
            },*/
            chart: {
                type: 'line',
                marginRight: 130,
                marginBottom: 25
            },
            navigation: {
            	buttonOptions: {
            		enabled: false
            	} 
            },
            title: {
                text: 'Statistiques des 7 derniers jours',
                x: -20
            },
            xAxis: {
            },
            yAxis: {
                title: {
                    text: 'Nombres'
                }
            },
            legend: {
                layout: 'horizontal',
                align: 'right',
                verticalAlign: 'top',
                x: -440,
                y: 328,
                borderWidth: 0
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.series.name + ' : ' + this.y +'</b><br/>'+ 'Le '+ this.x.toLowerCase();
                }
            }
        };

        Highcharts.theme = 
        {
		   colors: ['#27855e', '#e84200', '#EF3CF5', '#F5920C', '#396ADE'],
		   chart: {
		   		backgroundColor: 'none'
		   },
		   title: {
		        style: {
		            color: '#000',
		            fontWeight: 'bold'
		        }
		    },
		   xAxis: {
		      gridLineWidth: 1,
		      lineColor: '#000',
		      tickColor: '#000',
		      labels: {
		         style: {
		            color: '#000'
		         }
		      },
		      title: {
		         style: {
		            color: '#000',
		            fontWeight: 'bold',
		            fontSize: '12px'
		         }
		      }
		   },
		   yAxis: {
		      minorTickInterval: 'auto',
		      lineColor: '#000',
		      lineWidth: 1,
		      tickWidth: 1,
		      tickColor: '#000',
		      labels: {
		         style: {
		            color: '#000'
		         }
		      },
		      title: {
		         style: {
		            color: '#000',
		            fontSize: '12px'
		         }
		      }
		   },
		   legend: {
		      itemStyle: {
		         color: 'black'

		      },
		      itemHoverStyle: {
		         color: '#039'
		      },
		      itemHiddenStyle: {
		         color: 'gray'
		      }
		   }
		};

		var highchartsOptions = Highcharts.setOptions(Highcharts.theme);

        Highcharts.visualize(table, options);
	}

	// Fonction s'exécutant dès le chargement de la page

	// On appelle la fonction servant à lire les paramètres.
	// On appelle la fonction servant à afficher ou non la barre de progression.
    // On appelle la fonction servant à creer les Select du formulaire.
    // On appelle la fonction servant à remettre les informations dans les champs du formulaire.
    // on met un écouteur d'événement sur chacun des champs à fin de récupéré les valeurs une fois que le champ a perdu le focus ou au clic pour les boutons radio.
	
	$( function () {

		// -- onload routines
		putSettings();

		// Affiche un lien afin d'aider l'utilisateur à retourner au sommet de la page et fait apparaître le formulaire une fois le scroll limite atteint.
		$('body').prepend('<a href="#top" class="top_link" title="Revenir en haut de page"><i class="icon-up"></i></a>');
		$('.top_link').addClass('top_link');

		$(window).scroll( function()
		{  
		    var posScroll = $(document).scrollTop(); 

		    if( posScroll >=300 )  
		        $('.top_link').fadeIn(600);  
		    else  
		        $('.top_link').fadeOut(600);

			var distTop;

		    distTop = $('#userData').height() + 20;

		    if( posScroll >= distTop && $(window).width() > 520 ) 
		    {
	    		$('#fixedbug').css('display','inline-block'); 
	        	$('.fixed').css({'position' : 'fixed', 'width' : '28%'});       
		    } 
		    else 
		    { 
		    	$('.fixed').css({'position' : 'static', 'width' : '100%'});
		        $('#fixedbug').css('display','inline'); 
		    }
		} );
	
		initFormValues();

		progressBar();

		// Active la modalBox
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

		// Si une balise "a" possèdant la class btn est actionnée on appelle la fonction getUrl 
		$('a.btn').on("click", processURL);
		$('a.btn_img').on("click", processURL);

		// on appelle la fonction pour dessiner le graphique des statistiques que si on est sur la page stats.
		var tmp = document.location.href.split('/admin/');

		if ( tmp[1] == 'stats' )
			drawGraphicStats ();		

	} );

}( jQuery ) );
