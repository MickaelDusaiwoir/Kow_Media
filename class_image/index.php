<?php 
	
	// Déclaration du user agent 

	ini_set('user_agent', 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.16) Gecko/2009121601 Ubuntu/9.04 (jaunty) Firefox/3.0.16');

	// Incorporation des différentes classes

	include('ImageBaseDownloader.class.php');
	include('OfficeImageDownloader.class.php');
	include('RgbStockImageDownloader.class.php');
	include('GoogleImageDownloader.class.php');
	include('PhotoBucketDownloader.class.php');

	// Déclaration du type d'encodage des caractères

	header('content-type: text/html; charset=utf-8');

	// Initialisation de mes variables 

	$errors = array();
	$display = null;
	$displayErrors = null;
	$allResults = array();
	$selectedSites = array();

	// Génération du tableau comportant les données de la balise select  

	$dataOption   = array();
	$dataOption[] = array('IB', 'Image Base US');
	$dataOption[] = array('GI', 'Google Image US - FR');
	$dataOption[] = array('RGB', 'Rgb Stock US');
	$dataOption[] = array('OM', 'Office Microsoft US - FR');
	$dataOption[] = array('PB', 'Photo Bucket US');
	$dataOption[] = array('TLS', 'Tous Les Sites US - FR');


	// Je teste si je reçois un mot clé
	// Je teste si j'ai un site de sélectionner
	// je teste si j'ai un nombre de résultat souhaiter
	
	if ( isset($_POST['search']) ) 
		$keywords = $_POST['search'];
	else
		$keywords = '';

	if ( isset($_POST['site']) ) 
		$site =  $_POST['site'];
	else
		$site = null;

	if ( isset($_POST['NBR']) )
		$nbResult = $_POST['NBR'];
	else
		$nbResult = 20;
		
//	Je regarde quel site j'ai récupéré et je l'ajoute à mon tableau

	switch ( $site ) 
	{
		case 'IB':
			$selectedSites[] = new ImagebaseDownloader($keywords);
			break;

		case 'GI':
			$selectedSites[] = new GoogleImageDownloader($keywords);
			break;

		case 'RGB':
			$selectedSites[] = new RgbStockImageDownloader($keywords);
			break;

		case 'OM':
			$selectedSites[] = new OfficeImageDownloader($keywords);
			break;
		
		case 'PB':
			$selectedSites[] = new PhotoBucketDownloader($keywords);
			break;

		case 'TLS':
			$selectedSites[] = new ImagebaseDownloader($keywords);
			$selectedSites[] = new GoogleImageDownloader($keywords);
			$selectedSites[] = new RgbStockImageDownloader($keywords);
			$selectedSites[] = new OfficeImageDownloader($keywords);
			$selectedSites[] = new PhotoBucketDownloader($keywords);
			break; 
	}


//	Je parcour mon tableau contenant le(s) site(s) a parcourir 
//	J'initialise ma pagination qui sera créé a partir du nombre de résultat
//	Je compte le nombre de résultat afin de savoir si je peux traiter le contenu
//	Je récupère les résultats et je les ajoute à un tableau global 

	foreach ($selectedSites as $downloder) 
	{
		$downloder->setPagination($nbResult);

		if( ( $count = $downloder->search($errors) ) > 0 )
		{
			if( $results = $downloder->getResults() )
			{					
				$allResults = array_merge($allResults, $results);
			}
		}
		else 
		{
			//print_r($errors);
			$displayErrors .= '<div>';

			for ( $i=0; $i < count($errors) ; $i++ ) 
			{ 
				if ( $errors[$i][1] == 1 ) 
					$displayErrors .= '<p>Pas de mot clé saisi</p>';

				if ( $errors[$i][1] == 2 ) 
					$displayErrors .= '<p>Pas de nombre de résultat maximum saisi</p>';

				if ( $errors[$i][1] == 3 ) 
					$displayErrors .= "<p>Pas de contenu trouvé</p>";

				if ( $errors[$i][1] == 4 ) 
					$displayErrors .= "<p>Le début du bloc n'a pas été trouvé</p>";

				if ( $errors[$i][1] == 5 ) 
					$displayErrors .= "<p>La fin du bloc n'a pas été trouvée</p>";

				if ( $errors[$i][1] == 6 )
					$displayErrors .= "<p>La regex n'a rien trouvée</p>";
			}
			$displayErrors .= '</div>';
		}
	}


//	S'il y a quelque chose dans le tableau global je crée une liste dans laquelle j'ajoute tous les résultats


	if ( $allResults )
	{
		$display .= '<ul>'."\n";

		for ( $i=0; $i < $nbResult; $i++ ) 
		{ 
			$display .= '<li>'."\n";

			if ( $allResults[$i]['image'] ) 
			{
				$display .= 
					'<a href="'.$allResults[$i]['image']['url'].'">'.
					'<img src="'.$allResults[$i]['image']['thumb_url'].'" width="'.$allResults[$i]['image']['width'].'" '.
					'height="'.$allResults[$i]['image']['height'].'" />'.
					'</a>'."\n";
			}

			$display .= '</li>'."\n";
		}

		$display .= '</ul>'."\n";
	}
	
?>

<!DOCTYPE html>
<html>
	<head>
		<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">
		<title>Recherche</title>
		<link rel="stylesheet" type="text/css" href="./css/style.css" media="screen" />
		<meta name="viewport" content="width=device-width">
	</head>
	<body>
		<form action="#" method="post">
			<label>Rechercher sur</label>
			<select name="site">
				<?php 

					$options = null;

					for ( $i=0; $i < count($dataOption); $i++ ) 
					{ 
						$options .= '<option value="'.$dataOption[$i][0].'"';

						if ( $i == 0 ) 
							$options .='selected >';
						else
							$options .=' >';

						$options .= $dataOption[$i][1].'</option>';
					}

					echo($options);
				?>
			</select>
			<label>Nombre de r&eacute;sultat</label>
			<input type='number' name='NBR' value="<?php if ( $nbResult ) echo $nbResult; ?>"/>
			<label>Mots clés</label>
			<input type="text" name="search" value="<?php if ( $keywords !== '' ) echo $keywords; ?>" />
			<input type="submit" value="Search" />
		</form>

		<?php  
			echo $display;
		?>
		<script src="./js/jquery-1.9.1.min.js"></script>
		<script src="./js/script.js"></script>
	</body>
</html>