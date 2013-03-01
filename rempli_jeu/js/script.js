( function ( $ ) {

    "use strict";

// -- globals

    var data = new Array({'nom' : '', 'prenom' : '', 'jour': '', 'mois' : '', 'annee' : '', 'email' : '', 'adresse' : '', 'code_postal' : '', 'ville' : '' });

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

        //data[$(this).attr('name')] = $(this).val() ;

        tmp = new Array();

        if ( $(this).attr('name') !== '' )
        {
            champ = $(this).attr('name');

            if ( $(this).val() !== '' ) 
                valeur = $(this).val();
        }        

        data[champ] = valeur;

        console.log(data);

       // putData(data);
    }


    var putData = function ( url ) {

    }


    // Fonction s'exécutant dès le chargement de la page

	$( function () {

		// -- onload routines

        setSelect();

        $('#nom').on('blur',getData);
        $('#prenom').on('blur',getData);
        $('#jour').on('blur',getData);
        $('#mois').on('blur',getData);
        $('#annee').on('blur',getData);
        $('#email').on('blur',getData);
        $('#adresse').on('blur',getData);
        $('#cp').on('blur',getData);
        $('#ville').on('blur',getData);

		// Si une balise "a" être actionné on appelle la fonction getUrl 
		$('a').on("click", getUrl);

	} );

}( jQuery ) );