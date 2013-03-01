( function ( $ ) {

    "use strict";

// -- globals

    var userData = {'nom' : '', 'prenom' : '', 'jour': '', 'mois' : '', 'annee' : '', 'email' : '', 'adresse' : '', 'code_postal' : '', 'ville' : '' },
        key = new Array('nom' , 'prenom', 'jour', 'mois', 'annee', 'email', 'adresse', 'code_postal', 'ville');
// -- methods

    var setSelect = function () {

        var mois = new Array();

        mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre'];

        for ( var i = 0; i <= 11; i++ ) 
        {
            $('<option></option').attr('value', i).text(mois[i]).appendTo($('#mois'));
        };

        for ( var j = 1; j <= 31; j++ ) 
        {
            $('<option></option').attr('value', j).text(j).appendTo($('#jour'));
        };

        for ( var u = 1920; u <= 2030; u++ ) {
            $('<option></option').attr('value', u).text(u).appendTo($('#annee'));
        };

    }

    var getUrl = function ( e ) {

        var url;

        e.preventDefault();

        url = $(this).attr('href');

        putData(url);
    }

    var getData = function ( e ) {

        var tmp, champ, valeur;

        tmp = new Array();

        if ( $(this).attr('name') !== '' )
        {
            champ = $(this).attr('name');

            if ( $(this).val() !== '' ) 
                valeur = $(this).val();
        }   

        userData[champ] = valeur;

        console.log(userData);
    }


    var putData = function ( url ) {

        var data = {'nom' : '', 'prenom' : '', 'jour': '', 'mois' : '', 'annee' : '', 'email' : '', 'adresse' : '', 'code_postal' : '', 'ville' : '' },
            cookieArray = {};

        if ( document.cookie )
        {
            var tmp = document.cookie.split('=');
            cookieArray = JSON.parse(tmp[1]);         
        }

        for (var i = 0; i < key.length; i++) 
        {
            if ( userData[key[i]] && cookieArray[key[i]] ) 
            {
                if ( userData[key[i]] == cookieArray[key[i]] )
                {
                    data[key[i]] = cookieArray[key[i]];
                }
                else 
                {
                    data[key[i]] = userData[key[i]];
                }
            }
            else 
            {
                if ( cookieArray[key[i]] )
                {
                    data[key[i]] = cookieArray[key[i]];
                }
                else if ( userData[key[i]] )
                {
                    data[key[i]] = userData[key[i]];
                }
            }
        }
        
        console.log(document.cookie);

        createCookie(data);
        createUrl(data, url);

/*                 

// array to string 
userDataToString = JSON.stringify(userData);

expires.setTime(today.getTime() + (365*24*60*60*1000));
document.cookie = cookieName+ "=" + JSON.stringify(cookieArray) + ";expires=" + expires.toGMTString();

*/
    }

    var createCookie = function (data)
    {
        var cookieName = 'userData',
            today = new Date(),
            expires = new Date();

        // array to string 
        var dataToString = JSON.stringify(data);

        expires.setTime(today.getTime() + (365*24*60*60*1000));
        document.cookie = cookieName + "=" + JSON.stringify(dataToString) + ";expires=" + expires.toGMTString();
    }

    var createUrl = function (data, url)
    {
        for (var i = 0; i < key.length; i++) 
        {
            var tmp = '%' + key[i];
            url = url.replace(tmp, data[key[i]]);
        }

        console.log(url);
    }


    var setform = function () {

        var tmp = document.cookie.split('=');
        cookieArray = JSON.parse(tmp[1]); 

        for ( var i = 0; i < key.length; i++ )
        {
            var tmp = '#'+ key[i];
            $(tmp).attr('value', cookieArray[key[i]]);
        } 
    }


    // Fonction s'exécutant dès le chargement de la page

	$( function () {

		// -- onload routines

        if ( document.cookie )
        {
            setform();       
        }

        setSelect();

        $('#nom').on('blur',getData);
        $('#prenom').on('blur',getData);
        $('#jour').on('blur',getData);
        $('#mois').on('blur',getData);
        $('#annee').on('blur',getData);
        $('#email').on('blur',getData);
        $('#adresse').on('blur',getData);
        $('#code_postal').on('blur',getData);
        $('#ville').on('blur',getData);

		// Si une balise "a" être actionné on appelle la fonction getUrl 
		$('a').on("click", getUrl);

	} );

}( jQuery ) );