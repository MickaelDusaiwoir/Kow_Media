<?php 
	
	ini_set('user_agent', 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.16) Gecko/2009121601 Ubuntu/9.04 (jaunty) Firefox/3.0.16');

	include('ImageBaseDownloader.class.php');
	include('OfficeImageDownloader.class.php');
	include('RgbStockImageDownloader.class.php');
	include('GoogleImageDownloader.class.php');
	include('PhotoBucketDownloader.class.php');

	header('content-type: text/html; charset=utf-8');

	$errors = array();
	$display = null;
	$displayErrors = null;
	$allResults = array();
	
	if ( isset($_POST['search']) ) 
		$keywords = $_POST['search'];
	else
		$keywords = '';

	if ( isset($_POST['site']) ) 
		$site =  $_POST['site'];
	else
		$site = null;
	
	$selectedSites = array();

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

	if ( isset($_POST['NBP']) )
		$nbPage = $_POST['NBP'];
	else
		$nbPage = 1;

	foreach ($selectedSites as $downloder) 
	{
		$downloder->setPagination($nbPage);

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
					$displayErrors .= '<p>Pas de nombre de page saisi</p>';

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

	if ( $allResults )
	{
		$display .= '<ul>'."\n";

		foreach ( $allResults as $result ) 
		{
			if ( $result )
			{
				$display .= '<li>'."\n";

				if ( $result['image'] ) 
				{
					$display .= 
						'<a href="'.$result['image']['url'].'">'.
						'<img src="'.$result['image']['thumb_url'].'" width="'.$result['image']['width'].'" '.
						'height="'.$result['image']['height'].'" />'.
						'</a>'."\n";
				}

				$display .= '</li>'."\n";
			}
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
	</head>
	<body>
		<form action="#" method="post">
			<label>Rechercher sur</label>
			<select name="site">
				<option value='IB' selected >Image Base</option>
				<option value='GI'>Google Image</option>
				<option value='RGB'>Rgb Stock</option>
				<option value='OM'>Office Microsoft</option>
				<option value='PB'>Photo Bucket</option>
				<option value='TLS'>Tous Les Sites</option>
			</select>
			<label>Nombre de page</label>
			<input type='number' name='NBP' value="<?php if ( $nbPage ) echo $nbPage; ?>"/>
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