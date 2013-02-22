<?php 
	
	include('ImageBaseDownloader.class.php');
	include('OfficeImageDownloader.class.php');
	include('RgbStockImageDownloader.class.php');
	include('GoogleImageDownloader.class.php');
	include('PhotoBucketDownloader.class.php');

	header('content-type: text/html; charset=utf-8');

	$errors = array();
	$results = array();
	$display = null;
	
	if ( isset($_POST['search']) ) 
		$keywords = $_POST['search'];
	else
		$keywords = '';

	if ( isset($_POST['site']) ) 
		$site =  $_POST['site'];
	else
		$site = null;
	
	$selectedSite = array();

	switch ( $site ) 
	{
		case 'IB':
			$selectedSite[] = new ImagebaseDownloader($keywords);
			break;

		case 'GI':
			$selectedSite[] = new GoogleImageDownloader($keywords);
			break;

		case 'RGB':
			$selectedSite[] = new RgbStockImageDownloader($keywords);
			break;

		case 'OM':
			$selectedSite[] = new OfficeImageDownloader($keywords);
			break;
		
		case 'PB':
			$selectedSite[] = new PhotoBucketDownloader($keywords);
			break;

		case 'TLS':
			$selectedSite[] = new ImagebaseDownloader($keywords);
			$selectedSite[] = new GoogleImageDownloader($keywords);
			$selectedSite[] = new RgbStockImageDownloader($keywords);
			$selectedSite[] = new OfficeImageDownloader($keywords);
			$selectedSite[] = new PhotoBucketDownloader($keywords);
			break; 

	}


	if ( isset($_POST['NBP']) )
		$nbPage = $_POST['NBP'];
	else
		$nbPage = 1;


	// ok 
	//$img = new ImagebaseDownloader($keywords);
	//$img = new GoogleImageDownloader($keywords);
	//$img = new RgbStockImageDownloader($keywords);
	//$img = new OfficeImageDownloader($keywords);
	//$img = new PhotoBucketDownloader($keywords);

	//$img->setPagination(5);

	foreach ($selectedSite as $downloder) 
	{
		$img = $downloder;
		$img->setPagination($nbPage);

		if( ( $count = $img->search($errors) ) > 0 )
		{
			if( $results = $img->getResults() )
			{	
				$allResults = array();
				$allResults = array_merge($allResults, $results);

				$display .= '<ul>'."\n";

				foreach ( $allResults as $result ) 
				{
					if ( $result )
					{
						$display .= '<li>'."\n";

						if ( $result['image'] ) 
						{
							$src = isset($result['image']['url']) ? $result['image']['url'] : $result['image']['thumb_url'];

							$display .= 
								'<a href="'.$src.'">'.
								'<img src="'.$src.'" width="'.$result['image']['width'].'" '.
								'height="'.$result['image']['height'].'" />'.
								'</a>'."\n";
						}

						$display .= '</li>'."\n";
					}
				}

				$display .= '</ul>'."\n";
			}
		}
		else 
		{
			echo "erreur: ";
			print_r($errors);
		}
	}


?>

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
			<input type='number' name='NBP' />
			<label>Mots clés</label>
			<input type="text" name="search" />
			<input type="submit" value="Search" />
		</form>

		<?php  
			echo $display;
		?>

	</body>
</html>