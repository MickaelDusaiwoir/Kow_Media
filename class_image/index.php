<?php 
	
	include('ImageBaseDownloader.class.php');
	include('OfficeImageDownloader.class.php');
	include('RgbStockImageDownloader.class.php');
	include('GoogleImageDownloader.class.php');
	include('PhotoBucketDownloader.class.php');

	header('content-type: text/html; charset=utf-8');

	$results = array();
	$display = null;
	
	if ( isset($_POST['search']) ) 
		$keywords = $_POST['search'];
	else
		$keywords = '';

	//$img = new ImagebaseDownloader($keywords);
	//$img = new GoogleImageDownloader($keywords);
	//$img = new RgbStockImageDownloader($keywords);
	//$img->setPagination(5);

	// START

	$img = new OfficeImageDownloader($keywords);
	//$img->setLanguage(GenericImagesDownloader::English);

	// END

	//$img = new PhotoBucketDownloader($keywords);
	
	if ( $keywords )
	{
		if( ( $count = $img->search() ) > 0 )
		{
			if( $results = $img->getResults() )
			{
				$display .= '<ul>'."\n";
				foreach ( $results as $result ) 
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
			$display = 'Nothing was found with these keywords';
		}
	}
	else
	{
		echo "Aucun mot clé saisi";
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
			<input type="text" name="search" />
			<input type="submit" value="Search" />
		</form>

		<?php  
			echo $display;
		?>

	</body>
</html>