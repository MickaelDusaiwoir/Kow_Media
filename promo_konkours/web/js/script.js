function countClick ( id ) {
	jQuery.ajax({
		type: "POST",
		url: url_stats,
		data: {"contest_id" : id, "css" : settings['css'], "view" : settings['view']},
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
		defaultValue = {'civilite' : 'man' ,'nom' : 'Rambo', 'prenom' : 'John', 'jour': '20', 'mois' : '5', 'annee' : '1970', 'email' : 'john.rambo@exemple.be', 'adresse' : '62 Rue de Lille', 'code_postal' : '75343', 'ville' : 'Paris' };

	var mois = new Array();
		mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre'];

	var timeDelay = 3000,
		id_contest = '',
		url_contest = '',
		title_contest = '',
		img_contest = '',
		contests_played = [];
	
	var bool = false;
	
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
					$('#form #email').removeClass('error');
					userData[fieldName] = $(this).val();
					$(this).attr('class', 'ok');
				}
				else
				{
					if ( $('#form').find('.error').size() == 0 )
						$('#form #email').attr('class', 'error_email');
				}
			}
			else
			{
				userData[fieldName] = $(this).val();
				$(this).attr('class', 'ok');
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
						putUserInfos();
						break;
					}
					else if ( tmp[0] == " userData")
					{	
						userData = jQuery.parseJSON(tmp[1]);
						putUserInfos(); 						
						break;
					}
				}
			}
		}
		
		for ( var i = 0; i < key.length; i++ )
		{
			var fieldName = key[i];
			
			if ( fieldName == 'civilite' || fieldName == 'jour' || fieldName == 'mois' || fieldName == 'annee' || fieldName == 'email')
			{
				if ( fieldName == 'civilite' &&  userData['civilite'] !== ''  )
					$('#' + userData[fieldName]).attr('checked', 'checked');
				else if ( fieldName == 'civilite' &&  defaultValue['civilite'] !== ''  )
					$('#' + defaultValue[fieldName]).attr('checked', 'checked');

				if ( fieldName == 'mois' &&  userData['mois'] !== '' )
					$('option[name="' + mois[userData[fieldName] - 1] +'"]').attr('selected', 'selected').attr('class', 'ok');
				else if ( fieldName == 'email' && defaultValue[fieldName] !== '' )
					$('option[name="' + defaultValue[fieldName] +'"]').attr('selected', 'selected');

				if ( fieldName == 'jour' &&  userData['jour'] !== '' )
					$('option[name="' + userData[fieldName] +'"]').attr('selected', 'selected').attr('class', 'ok');
				else if ( fieldName == 'email' && defaultValue[fieldName] !== '' )
					$('option[name="' + defaultValue[fieldName] +'"]').attr('selected', 'selected');

				if ( fieldName == 'annee' &&  userData['annee'] !== '' )
					$('option[name="' + userData[fieldName] +'"]').prop('selected','selected').attr('class', 'ok');
				else if ( fieldName == 'email' && defaultValue[fieldName] !== '' )
					$('option[name="' + defaultValue[fieldName] +'"]').prop('selected','selected');

				if ( fieldName == 'email' && userData[fieldName] !== '' )
					$('#'+ fieldName).attr('value', userData[fieldName]).attr('class', 'ok');
				else if ( fieldName == 'email' && defaultValue[fieldName] !== '' )
					$('#'+ fieldName).attr('placeholder', defaultValue[fieldName]).attr('class','error');
			}
			else
			{
				if ( userData[fieldName] !== '' )
					$('#'+ fieldName).attr('value', userData[fieldName]).attr('class', 'ok');
				else if ( defaultValue[fieldName] !== '' )
					$('#'+ fieldName).attr('placeholder', defaultValue[fieldName]).attr('class', 'error');
			}
		} 
	}


	// Cette fonction permet l'affichage d'une barre de progression si le paramètre pb est égal à 1.
	// Cette dernière calcule le nombre d'input afin de pouvoir déduire la valeur en pourcentage d'une donnée.
	// Elle lit le cookie afin de savoir combien de champs ont été remplis et affiche en conséquence le pourcentage de remplissage du formulaire.
	// Si elle est à 100% on affiche un message et on modifie par la même occasion l'affichage.

	var progressBar = function () 
	{
		var nbInputText = 0, nbInputEmail = 0, nbInputs = 0, formComplete = new Array;
		nbInputText = $('#userData').find('input[type="text"]').length;
		nbInputEmail = $('#userData').find('input[type="email"]').length;
		nbInputs = nbInputText + nbInputEmail ;
		
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
									if ( key[y] !== 'jour' && key[y] !== 'mois' && key[y] !== 'annee' )
										totalPourcentage += pourcentage;
								}
							}
							break;
						}
					}
				}
			}

			$('progress').attr('value', totalPourcentage);
			$('#progress p span').text(Math.floor(totalPourcentage));

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

		var url_css = $('#design_css').attr('href'),
			modal_css = $('#modal_css').attr('href');

		// changer le css de la page
		if ( settings['view'] == 0 )
		{
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
		}

		// changer le css de la modal box
		switch ( settings['modal_css'] )
		{
			case 1:
				modal_css = modal_css.replace('modal.css', 'modal1.css');
				$('#modal_css').attr('href', modal_css);
				break;

			case 2:
				modal_css = modal_css.replace('modal.css', 'modal2.css');
				$('#modal_css').attr('href', modal_css);
				break;

			case 3:
				modal_css = modal_css.replace('modal.css', 'modal3.css');
				$('#modal_css').attr('href', modal_css);
				break;

			case 0:
			default :
				break;
		}
		
		// changer le texte des liens
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
				$('.astuces .btn').text('Je valide !');
				break;
		}

		// changer le texte du titre ( étape 1 )
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
				$('#userData h2 span').text('Complétez');
				break;
		}

		// changer le texte du titre ( étape 2 )
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

			case 0:
			default:
				$('#contests h2 span').text('Jouez');
				break;
		}

		// ajouter un texte apres le titre ( étape 1 )
		switch ( settings['after_title1'] )
		{
			case 1:
				$('#userData h2 em').append(' et jouez');
				break;

			case 2:
				$('#userData h2 em').append(' et gagnez');
				break;

			case 3:
				$('#userData h2 em').append(' et amusez-vous');
				break;

			case 0:
			default:
				$('#userData h2 em').append('');
				break;
		}	

		// ajouter un texte apres le titre ( étape 2 )
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
				$('#contests h2 em').append('');
				break;
		}			

		// définir quelle publicité afficher
		switch ( settings['pub'] ) 
		{
			case 1:
				$('.pub').addClass('no_show');
				$('#pub_1').addClass('show','pub').removeClass('no_show');
				break;

			case 2:
				$('.pub').addClass('no_show');
				$('#pub_2').addClass('show','pub').removeClass('no_show').css('display', 'block'); 
				break;
				
			case 3:
				$('#pub_1').addClass('show','pub');
				$('#pub_2').addClass('show','pub').removeClass('no_show').css('padding-bottom', '8px');
				break;

			case 0:
				$('.pub').addClass('no_show');
				$('#intro').addClass('no_show');
				break;
				
			default:			
				break;
		}

		// définir si on affiche ou pas les signiatures pour la pub n°2
		switch ( settings['sign'] )
		{
			case 0 : 
				$('.signature').attr('class', 'no_show');
				break;

			case 1:
			default:
				break;
		}

		// Définir quel titre on affiche dans le formulaire.
		if ( settings['view'] !== 0 )
		{
			switch ( settings['form_title'] )
			{
				case 1 :
					$('#titleForm').text('Complétez les informations');
					break;

				case 0 :
				default :
					$('#titleForm').text('Où envoyer les cadeaux ?');
					break;

			}
		}

		switch ( settings['footer'] )	
		{
			case 1 :
				$('#footer').css('display', 'inline-block');
				break;

			case 0 :
			default : 
				$('#footer').css('display', 'none');
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
	
	var waitTimer = function ()
		{
			// ajout d'une class lorsque le concours a été jouer
			$('#modal-played a').attr('href', url_contest).attr('title', title_contest);
			$('#loaderBox a').attr('href', url_contest).find('small').text(title_contest);
			
			$('#modal-formulaire #formulaire').fadeOut('fast');
			$('#modal-formulaire #loaderBox').fadeIn().delay(timeDelay).fadeOut();

			$('#modal-formulaire').delay(timeDelay).fadeOut().removeData('modal');
			$('.modal-backdrop').delay(timeDelay).fadeOut();
			
			$('#formulaire').css('display', 'block');
	/*		
			$('#modal-formulaire').removeClass('in').attr('aria-hidden', 'true').css('display', 'none');
			$('.modal-backdrop').remove();
	*/
		
			changeValueLink();
			
			putUserInfos();
			// affichage des infos de l'utilisateur une fois qu'il a cliqué pour participer à un concours
		}


	var setRequest = function ( e ) 
	{
		e.preventDefault();
		e.stopPropagation();

		if ( userData['nom'] == '' && userData['prenom'] == '' )
		{	
			if ( settings['alert_data'] == '1')
				alert('ATTENTION, remplissez le formulaire pour que tous les concours soient complétés AUTOMATIQUEMENT avec vos informations.');
			url_contest = buildRequest(defaultValue, url_contest);
		}
		else
		{
			url_contest = buildRequest(userData, url_contest);
		}		

		waitTimer();			
	}
	
	var setCookieContest = function(e)
	{
		var tmp = $(this).attr('data-id');

		contests_played.push(tmp);

		var today = new Date();
		var expires = new Date();

		expires.setTime(today.getTime() + (365*86400));

		// array to string
		var dataToString = JSON.stringify(contests_played);
		
		document.cookie = "contests_played=" + dataToString + ";expires=" + expires.toGMTString()+"; path=/";
		
		contestsPlayed(); 
	}
	
	// Fonction qui va changer le lien. Au début çà dirige vers la modal box. Ensuite, les liens se dirigent vers le concours directement
	var changeValueLink = function()
	{
		var linksCount = $('#contests a.img').size();
		var aLinks = $('#contests a.img');
		var i;
		
		for ( i = 0; i < linksCount; i++ )
		{
			var href = aLinks.eq(i).attr('rel');
			aLinks.eq(i).attr('href', href);
			aLinks.eq(i).addClass('getContest').attr('target', '_blank').attr('title', 'Participez au concours').attr('data-toggle', 'tooltip').attr('data-original-title', 'Participez au concours').attr('onclick','javascript:countClick('+aLinks.eq(i).attr('data-id')+'); _gaq.push([\'_trackPageview\',\'/clic.html\']);').parents('figure').css('overflow', 'visible');
			aLinks.tooltip('hide');
		}
		addEventLink();
	}
	
	var showDataUser = function()
	{
		var i;
		var aFieldsId = new Array('user_civilite', 'user_nom' , 'user_prenom', 'user_email', 'user_adresse', 'user_cp', 'user_ville');
		var moisLettre = mois[userData['mois'] - 1];
		var ddn = userData['jour'] + ' ' + moisLettre + ' ' + userData['annee'];
		
		var moisLettreDefault = mois[defaultValue['mois'] - 1];
		var ddnDefault = defaultValue['jour'] + ' ' + moisLettreDefault + ' ' + defaultValue['annee'];

		switch(userData['civilite']) 
			{
				case 'woman':
					$('#user_civilite').text('Mme');
					break;
					
				case 'man':
					$('#user_civilite').text('M.');
					break;
					
				case 'miss':
					$('#user_civilite').text('Mlle');
					break;
			}
			
		if(userData['nom'] !== '')
			$('#user_nom').text(userData['nom']);
		else 
			$('#user_nom').text(defaultValue['nom']);
		
		if(userData['prenom'] !== '')		
			$('#user_prenom').text(userData['prenom']);
		else
			$('#user_prenom').text(defaultValue['prenom']);
				
		if(userData['email'] !== '')		
			$('#user_email').text(userData['email']);
		else
			$('#user_email').text(defaultValue['email']);
			
		if(userData['adresse'] !== '')		
			$('#user_adresse').text(userData['adresse']);
		else
			$('#user_adresse').text(defaultValue['adresse']);
			
		if(userData['code_postal'] !== '')		
			$('#user_cp').text(userData['code_postal']);
		else
			$('#user_cp').text(defaultValue['code_postal']);
			
		if(userData['ville'] !== '')		
			$('#user_ville').text(userData['ville']);
		else
			$('#user_ville').text(defaultValue['ville']);
		
		if(userData['jour'] !== '' || userData['mois'] !== '' || userData['annee'] !== '')		
			$('#user_ddn').text(ddn);
		else
			$('#user_ddn').text(ddnDefault);
	}
	
	var setEventForm = function () 
	{
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
		
		$('a.btn').on("click", setCookieContest);
		$('a.btn_img').on("click", setCookieContest);
	}
	
	var backToTop = function () 
	{
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
	}
	

//////////////////////////////////////////////////////////////////////////////////////////////////////  modal suppresion

	var addLinkContest = function ()
	{
		$('#modal-formulaire #loaderBox').hide();
		$('#formulaire').fadeIn('fast');
		
		url_contest = $(this).attr('rel');
		id_contest = $(this).attr('data-id');
		bool = true;
	}
	
	var contestsPlayed = function()
	{
		
		for ( var i = 0; i < contests_played.length; i++ )
		{
			$('a[data-id='+ contests_played[i] +']').parent().addClass('played');
			$('a[data-id='+ contests_played[i] +']').parents('.contest').addClass('contestPlayed');
		}
	};
	
	var checkContestsCookie = function ()
	{
		if ( document.cookie )
		{
			if ( cookie ('contests_played') ) 
			{
				contests_played = JSON.parse(cookie ('contests_played'));
				contestsPlayed();
				putUserInfos();
			}
		}
		
	};
	
	var addEventLink = function ()
	{
		$('a.btn').on("click", processURL);
		$('a.btn_img').on("click", processURL);
		$('a.getContest').on("click", processURL);
		$('a.getContest').on("click", setCookieContest);
	};
	
	var putUserInfos = function ()
	{
		if ( settings['view'] !== 0 )
			$('#intro').hide();

		$('#infosUser').css('display', 'inline-block');
		showDataUser();
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
		
		initFormValues();

		setEventForm();
		
		progressBar();

		backToTop();
		
		checkContestsCookie();
		

		// Active la modalBox
		if ( settings['mb'] == 1 )
		{
			$('#dialog-message').modal('toggle');	
			
			// Lien de recdirection "OUI" "NON" de la modal box
			$('#btnNo').on('click', function ( e ){
				e.preventDefault();
				window.location = 'http://wwww.konkours.com';
			});
		}
			
		
		// Si une balise "a" possèdant la class btn est actionnée on appelle la fonction getUrl 
		$('a.btn').on("click", processURL);
		$('a.btn_img').on("click", processURL);
		

		// on appelle la fonction pour dessiner le graphique des statistiques que si on est sur la page stats.
		var tmp = document.location.href.split('/admin/');

		if ( tmp[1] == 'stats' )
			drawGraphicStats ();
		
		if ( bool == false )
		{
			$('.img').on('click', addLinkContest);
		}
		else 
		{
			changeValueLink();
		}
		
		// on appel la fonction qui va créer l'url
		$('#modal-formulaire input[type=submit]').on('click', setRequest);

		$('#btn_infos').on('click', function(e){
			$('#modal-formulaire').css('display', 'block');
			$('#modal-formulaire').attr('aria-hidden', 'false').addClass('in').find('#formulaire').show();
			$('#loaderBox').hide();
		});


		$('#form button').on('click', function ( e ) {
			e.preventDefault();
		});
		
	} );

}( jQuery ) );
